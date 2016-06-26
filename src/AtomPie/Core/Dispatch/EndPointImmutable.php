<?php
namespace AtomPie\Core\Dispatch {

    use AtomPie\Boundary\Core\Dispatch\IAmEndPointValue;
    use AtomPie\Core\NamespaceHandler;
    use AtomPie\Web\Connection\Http\ImmutableUrl;
    use AtomPie\Web\Connection\Http\Url;

    class EndPointImmutable extends QueryString implements IAmEndPointValue
    {

        const DEFAULT_METHOD = '__default';
        const DEFAULT_SEPARATOR = '.';

        private $sEndPointClass;
        private $sEndPointMethod;
        private $aEndPointNamespaces;
        private $aEndPointClasses;

        public function __construct($sEndPointUrl, array $aEndPointNamespaces = null, array $aEndPointClasses = null)
        {

            if (false !== strstr($sEndPointUrl, self::DEFAULT_SEPARATOR)) {
                list($sClass, $sMethod) = explode(self::DEFAULT_SEPARATOR, $sEndPointUrl);
                if (!is_null($sMethod)) {
                    $this->validateMethod($sMethod);
                } else {
                    throw new EndPointException('Invalid EndPoint query structure.');
                }
            } else {
                if (strlen($sEndPointUrl) > 0) {
                    $sClass = $sEndPointUrl;
                    $sMethod = self::DEFAULT_METHOD; // This is default EndPoint method
                } else {
                    throw new EndPointException('Invalid EndPoint query structure.');
                }
            }

            $this->sEndPointMethod = $sMethod;
            $this->sEndPointClass = self::escape($sClass);
            $this->validateClassType($this->sEndPointClass);

            $this->aEndPointNamespaces = $aEndPointNamespaces;
            $this->aEndPointClasses = $aEndPointClasses;

        }

        /**
         * @return string
         */
        public function getClassString()
        {
            return $this->sEndPointClass;
        }

        /**
         * @return string
         */
        public function getMethodString()
        {
            return $this->sEndPointMethod;
        }

        /**
         * @return bool
         */
        public function isDefaultMethod()
        {
            return $this->sEndPointMethod == self::DEFAULT_METHOD;
        }

        /**
         * @return bool
         */
        public function hasMethod()
        {
            return !empty($this->sEndPointMethod);
        }

        /**
         * @return string
         */
        public function __toString()
        {
            return $this->getClassString() . self::DEFAULT_SEPARATOR . $this->getMethodString();
        }

        /**
         * @return string
         */
        public function __toUrlString()
        {
            $oShortener = new NamespaceHandler($this->aEndPointNamespaces, $this->aEndPointClasses);
            $sEndPointClass = $oShortener->shorten($this->getClassString());

            return self::urlEscape($sEndPointClass) . self::DEFAULT_SEPARATOR . $this->getMethodString();
        }

        /**
         * Returns instance of EndPointUrl filled with EndPoint string.
         *
         * @return EndPointUrl
         */
        public function cloneEndPointUrl()
        {
            return new ImmutableUrl($this->__toUrlString());
        }

        private function validateClassType($sClassType)
        {

            // Class

            if (!is_string($sClassType)) {
                throw new EndPointException("Dispatch command must be string.");
            }
            if (!preg_match('/^[a-zA-Z0-9|_|\\\]+$/', $sClassType) > 0) {
                throw new EndPointException(sprintf('Class type: [%s] contains not allowed chars! Only a-Z, digits, [_\\] allowed.',
                    $sClassType));
            }

        }

        private function validateMethod($sMethod)
        {

            // Event/Action

            if (!is_string($sMethod)) {
                throw new EndPointException('Dispatch action/event must be string.');
            }

            if (!preg_match('/^[a-zA-Z0-9|_|\]\[]+$/', $sMethod) > 0) {
                throw new EndPointException(sprintf('Method: [%s] contains not allowed chars! Only a-Z, digits, [_] allowed.',
                    $sMethod));
            }

        }

    }

}