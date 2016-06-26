<?php
namespace AtomPie\Core {

    use AtomPie\Boundary\Core\IAmApplicationConfig;
    use AtomPie\Core\Config\Exception;
    use AtomPie\Boundary\System\IAmEnvVariable;
    use Generi\Boundary\IConstrainValueType;

    /**
     * Class Config
     */
    abstract class ApplicationConfig implements IAmApplicationConfig
    {

        /**
         * @var array
         */
        private $aValues = [];

        abstract public function __construct(IAmEnvVariable $oEnv);

        public function __get($sName)
        {
            if ($this->__isset($sName)) {
                return $this->aValues[$sName];
            }

            throw new Exception(sprintf('Config key [%s] is not set', $sName));
        }

        public function __set($sName, $sValue)
        {
            throw new Exception('Can not set values! Config is read only.');
        }

        public function __unset($sName)
        {
            throw new Exception('Can not remove values! Config is read only.');
        }

        /**
         * @param $sName
         * @param $sValue
         * @throws Exception
         */
        protected function set($sName, $sValue)
        {
            if ($this instanceof IConstrainValueType) {
                $aConstrainSpec = $this->__constrainSpec();
                if (isset($aConstrainSpec[$sName])) {
                    $sType = $aConstrainSpec[$sName];
                    if (!$sValue instanceof $sType) {
                        throw new Exception(sprintf('Property [%s] must be of type [%s].', $sName, $sType));
                    }
                }
            }

            $this->aValues[$sName] = $sValue;
        }

        /**
         * Overrides base value
         *
         * @param $sName
         * @param $sValue
         * @throws Exception
         */
        protected function override($sName, $sValue)
        {
            if (!$this->__isset($sName)) {
                throw new Exception(
                    sprintf('Property [%s] requires parent value to exist. Configuration class [%s] may lack key [%s].',
                        $sName,
                        get_parent_class($this),
                        $sName
                    )
                );
            }

            $this->set($sName, $sValue);
        }

        /**
         * Return true if any value is set. Null is also value.
         *
         * @param $sName
         * @return bool
         */
        public function __isset($sName)
        {
            return array_key_exists($sName, $this->aValues);
        }

        /**
         * @param $sName
         */
        protected function remove($sName)
        {
            unset($this->aValues[$sName]);
        }

    }

}
