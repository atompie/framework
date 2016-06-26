<?php
namespace AtomPie\Service\Object {

    use Generi\Boundary\IObject;

    class DeEncapsulator
    {

        /**
         * @var \ReflectionObject
         */
        private $oReflectionObject;

        /**
         * @var IObject
         */
        private $oDomain;

        public function __construct(IObject $oDomain)
        {
            $this->oDomain = $oDomain;
            $this->oReflectionObject = new \ReflectionObject($oDomain);
        }

        public function __set($sName, $sValue)
        {
            $oProperty = $this->oReflectionObject->getProperty($sName);
            $oProperty->setAccessible(true);
            $oProperty->setValue($this->oDomain, $sValue);
        }

        public function __get($sName)
        {
            $oProperty = $this->oReflectionObject->getProperty($sName);
            $oProperty->setAccessible(true);
            return $oProperty->getValue($this->oDomain);
        }

        public function __isset($sName)
        {
            return $this->oReflectionObject->hasProperty($sName);
        }

        public function __unset($sName)
        {
            $this->__set($sName, null);
        }

        public function __call($sName, $aParams)
        {
            $oMethod = $this->oReflectionObject->getMethod($sName);
            $oMethod->setAccessible(true);
            return $oMethod->invokeArgs($this->oDomain, $aParams);
        }

    }

}
 