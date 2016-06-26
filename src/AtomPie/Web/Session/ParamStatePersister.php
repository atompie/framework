<?php
namespace AtomPie\Web\Session {

    use AtomPie\Web\Boundary\IPersistParamState;
    use AtomPie\Web\Boundary\IAmRequestParam;
    use Generi\Boundary\IAmKeyValueStore;

    class ParamStatePersister implements IPersistParamState
    {

        const I_PERSIST_KEY = '@IPersistValue';
        const VALUE = 'Value';
        const COLLECTION = 'Collection';
        const DISTINCT_COLLECTION = 'DistinctCollection';

        /**
         * @var IAmKeyValueStore
         */
        private $oSession;

        /**
         * @var string
         */
        private $sNamespace;

        public function __construct(IAmKeyValueStore $oSession, $sNamespace)
        {
            $this->oSession = $oSession;
            $this->sNamespace = $sNamespace;
        }

        /**
         * Loads state from session.
         *
         * @param $sParamName
         * @param string $sContext
         * @return mixed|null
         */
        public function loadState($sParamName, $sContext = 'global-context')
        {

            $sKey = $this->getSessionKey($sParamName);
            if ($this->oSession->has($sKey)) {
                $sIPersistContainer = $this->oSession->get($sKey);

                if (isset($sIPersistContainer[$sContext])) {
                    return $sIPersistContainer[$sContext];
                }

            }

            return null;

        }

        /**
         * Saves state to session.
         *
         * @param IAmRequestParam $oParam
         * @param null $sAs
         * @param string $sContext
         */
        public function saveState(IAmRequestParam $oParam, $sAs = null, $sContext = 'global-context')
        {

            if (null === $sAs) {
                $sAs = self::VALUE;
            }

            $sSessionKey = $this->getSessionKey($oParam->getName());
            if ($sAs == self::VALUE) {

                $mSessionValue = $this->oSession->get($sSessionKey);

                if ($oParam->isArray()) {

                    if (!is_array($mSessionValue[$sContext])) {
                        $mSessionValue[$sContext] = array();
                    }
                    foreach ($oParam->getValue() as $sParamKey => $mParamValue) {
                        $mSessionValue[$sContext][$sParamKey] = $mParamValue;
                    }
                    $this->oSession->set($sSessionKey, $mSessionValue);

                } else {
                    // Replace
                    $mParamValue = $oParam->getValue();
                    $mSessionValue[$sContext] = $mParamValue;
                    $this->oSession->set($sSessionKey, $mSessionValue);
                }

            } else {
                if ($sAs == self::DISTINCT_COLLECTION) {

                    $mSessionValue = $this->oSession->get($sSessionKey);
                    if (!is_array($mSessionValue)) {
                        $mSessionValue = array();
                    }

                    if (!is_array($mSessionValue[$sContext])) {
                        $mSessionValue[$sContext] = array();
                    }

                    if ($oParam->isArray()) {
                        foreach ($oParam->getValue() as $sParamKey => $mParamValue) {
                            $mSessionValue[$sContext][$sParamKey] = $mParamValue;
                        }
                    } else {
                        $mParamValue = $oParam->getValue();
                        $mSessionValue[$sContext][$mParamValue] = $mParamValue;
                    }

                    $this->oSession->set($sSessionKey, $mSessionValue);

                } else {
                    if ($sAs == self::COLLECTION) {

                        $mSessionValue = $this->oSession->get($sSessionKey);
                        if (!is_array($mSessionValue)) {
                            $mSessionValue = array();
                        }

                        if (!is_array($mSessionValue[$sContext])) {
                            $mSessionValue[$sContext] = array();
                        }

                        if ($oParam->isArray()) {
                            foreach ($oParam->getValue() as $sParamKey => $mParamValue) {
                                $mSessionValue[$sContext][] = $mParamValue;
                            }
                        } else {
                            $mParamValue = $oParam->getValue();
                            $mSessionValue[$sContext][] = $mParamValue;
                        }

                        $this->oSession->set($sSessionKey, $mSessionValue);

                    }
                }
            }

        }

        /**
         * Removes param value from session.
         *
         * If you set $bRemoveAllValuesRegardlessContext to TRUE
         * all param values will be removed regardless the context.
         *
         * So if there is param A with 10 values for different components
         * all values will be removed.
         *
         * @param $sParamName
         * @param bool $bRemoveAllValuesRegardlessContext
         * @param string $sContext
         */
        public function removeState(
            $sParamName,
            $bRemoveAllValuesRegardlessContext = false,
            $sContext = 'global-context'
        ) {

            $sKey = $this->getSessionKey($sParamName);

            if ($sContext == 'global-context' || $bRemoveAllValuesRegardlessContext) {

                // Remove all values as global context has 1 value

                $this->oSession->remove($sKey);

            } else {
                if ($this->oSession->has($sKey)) {

                    // Remove only context value

                    $sIPersistContainer = $this->oSession->get($sKey);
                    unset($sIPersistContainer[$sContext]);
                    $this->oSession->set($sKey, $sIPersistContainer);
                }
            }

        }

        /**
         * @param $sParamName
         * @return bool
         */
        public function hasState($sParamName)
        {
            $sKey = $this->getSessionKey($sParamName);
            return $this->oSession->has($sKey);
        }

        private function getSessionKey($sParamName)
        {
            return self::I_PERSIST_KEY . '/' . $this->sNamespace . '/' . $sParamName;
        }


    }

}
