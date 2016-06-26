<?php
namespace AtomPie\DependencyInjection {

    use AtomPie\DependencyInjection\Boundary\IAmDependencyMetaData;

    class DependencyInvokeMetaData implements IAmDependencyMetaData
    {

        private $oObject;
        private $sCalledClassType;
        private $sCalledMethod;
        /**
         * @var \ReflectionFunctionAbstract
         */
        private $oCalledMethodOrFunction;
        /**
         * @var \ReflectionParameter
         */
        private $oParam;

        /**
         * @param $oObject
         * @param $sCalledClassType
         * @param $sCalledMethod
         * @param \ReflectionFunctionAbstract $oCalledMethodOrFunction
         * @param \ReflectionParameter $oParam
         */
        public function __construct(
            $oObject,
            $sCalledClassType,
            $sCalledMethod,
            \ReflectionFunctionAbstract $oCalledMethodOrFunction,
            \ReflectionParameter $oParam
        ) {
            $this->oObject = $oObject;
            $this->sCalledClassType = $sCalledClassType;
            $this->sCalledMethod = $sCalledMethod;
            $this->oParam = $oParam;
            $this->oCalledMethodOrFunction = $oCalledMethodOrFunction;
        }

        /**
         * @return mixed
         */
        public function getCalledMethod()
        {
            return $this->sCalledMethod;
        }

        /**
         * @return object
         */
        public function getObject()
        {
            return $this->oObject;
        }

        /**
         * @return mixed
         */
        public function getCalledClassType()
        {
            return $this->sCalledClassType;
        }

        /**
         * @return \ReflectionParameter
         */
        public function getParamMetaData()
        {
            return $this->oParam;
        }

        /**
         * @return \ReflectionFunctionAbstract
         */
        public function getCalledFunctionMetaData()
        {
            return $this->oCalledMethodOrFunction;
        }

        /**
         * @return bool
         */
        public function isClass()
        {
            return $this->oCalledMethodOrFunction instanceof \ReflectionClass;
        }

    }

}
