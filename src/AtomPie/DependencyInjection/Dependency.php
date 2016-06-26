<?php
namespace AtomPie\DependencyInjection {

    use AtomPie\DependencyInjection\Boundary\IDefineDependency;
    use AtomPie\I18n\Label;

    class Dependency implements IDefineDependency
    {

        const TYPE_LESS = '*';
        const METHOD_LESS = '#';
        const CLOSURE = '$';

        private $aDependencies = array();

        /**
         * @param array $aDependencies
         * @return $this
         * @throws Exception
         */
        public function setDependency(array $aDependencies)
        {
            if (empty($this->aDependencies)) {
                $this->replaceDependency($aDependencies);
                return $this;
            } else {
                throw new Exception(new Label('Dependencies can be set only on empty container. Use addDependency or replaceDependency.'));
            }
        }

        /**
         * @param array $aDependencies
         * @return $this
         */
        public function addDependency(array $aDependencies)
        {
            $this->aDependencies = array_merge($this->aDependencies, $aDependencies);
            return $this;
        }

        /**
         * @param array $aDependencies
         */
        public function replaceDependency(array $aDependencies)
        {
            $this->aDependencies = $aDependencies;
        }

        /**
         * @return array
         */
        public function getDependencies()
        {
            return $this->aDependencies;
        }

        public function hasTypeLessDependency()
        {
            return isset($this->aDependencies[self::TYPE_LESS]);
        }

        /**
         * @param $sDependencyClassType
         * @return bool
         */
        public function hasDependency($sDependencyClassType)
        {
            foreach ($this->aDependencies as $sClassType=>$aMethodDependencies) {
                if($sClassType == $sDependencyClassType) {
                    return true;
                }
            }
            return false;
        }
        
        /**
         * @return \Closure
         */
        public function getTypeLessDependency()
        {
            return $this->aDependencies[self::TYPE_LESS];
        }

        /**
         * @param IDefineDependency $oDependency
         */
        public function merge(IDefineDependency $oDependency)
        {
            $this->aDependencies = array_merge($this->aDependencies, $oDependency->getDependencies());
        }

    }

}
