<?php
namespace AtomPie\DependencyInjection {

    use AtomPie\DependencyInjection\Boundary\IConstructInjection;

    /**
     * Class DependencyContainer
     * @package AtomPie\DependencyInjection
     */
    class DependencyContainer implements IConstructInjection
    {

        /**
         * Holds types, and methods for dependencies.
         * First key is type, second is method.
         *
         * @var array
         */
        private $aContainer = array();

        /**
         * DependencyContainer constructor.
         * @internal
         */
        public function __construct()
        {
        }

        /**
         * @param $sClassType
         * @param $sMethod
         * @return Dependency
         */
        public function forMethod($sClassType, $sMethod)
        {
            if (!$this->existsExactly($sClassType, $sMethod)) {
                $oDependency = new Dependency();
                $this->aContainer[$sClassType][$sMethod] = $oDependency;
            }
            return $this->aContainer[$sClassType][$sMethod];
        }

        /**
         * @param $sClassType
         * @return Dependency
         */
        public function forAnyMethodInClass($sClassType)
        {
            return $this->forMethod($sClassType, Dependency::METHOD_LESS);
        }

        /**
         * @return Dependency
         */
        public function forAnyClass()
        {
            return $this->forMethod(Dependency::TYPE_LESS, Dependency::METHOD_LESS);
        }

        /**
         * @return array
         */
        public function getContainer()
        {
            return $this->aContainer;
        }

        /**
         * @param $sFunctionId
         * @return Dependency
         */
        public function forFunction($sFunctionId)
        {
            return $this->forMethod(Dependency::CLOSURE, $sFunctionId);
        }

        /**
         * @param $sClassType
         * @param $sMethod
         * @param $sType
         * @return bool
         */
        public function hasDependency($sClassType, $sMethod, $sType)
        {
            /** @var Dependency $oDependency */
            $oDependency = $this->getInjectionClosureFor($sClassType, $sMethod);
            if ($oDependency !== false) {
                $aDependencies = $oDependency->getDependencies();
                return isset($aDependencies[$sType]);
            }

            return false;
        }

        /**
         * @param $sClassType
         * @param $sMethod
         * @return array
         */
        public function getDependencies($sClassType, $sMethod)
        {
            /** @var Dependency $oDependency */
            $oDependency = $this->getInjectionClosureFor($sClassType, $sMethod);
            if (false === $oDependency) {
                return array();
            }
            return $oDependency->getDependencies();
        }

        public function merge(DependencyContainer $oNewContainer)
        {
            foreach ($oNewContainer->getContainer() as $sClassType => $mClassTypeData) {
                /**
                 * @var string $sMethod
                 * @var Dependency $oThisDependency
                 */
                foreach ($mClassTypeData as $sMethod => $oNewDependency) {
                    $this->addDependency($sClassType, $sMethod, $oNewDependency);
                }
            }
        }

        /**
         * @param $sClassType
         * @param $sMethod
         * @return bool
         */
        private function existsExactly($sClassType, $sMethod)
        {
            return isset($this->aContainer[$sClassType][$sMethod]);
        }

        /**
         * @return bool
         */
        private function existsClosure()
        {
            return isset($this->aContainer[Dependency::CLOSURE]);
        }

        private function getDependencyMatch($sClassType, $sMethod)
        {
            $aMergedDependencies = $this->getTypeLessDependencyArray($sMethod);
            if(!$this->isTypeLess($sClassType)) {
                $aDependencyCollection2 = $this->getTypeFulDependencyArray($sClassType, $sMethod);
                $aMergedDependencies = array_merge($aMergedDependencies,$aDependencyCollection2);
            }
            return $this->mergeDependencies($aMergedDependencies);
        }

        private function getDependencyMatchForTypeFulDependency($sClassType, $sMethod)
        {
            $aDependencyCollection = $this->getTypeFulDependencyArray($sClassType, $sMethod);
            return $this->mergeDependencies($aDependencyCollection);
        }

        /**
         * @param $sClassType
         * @param $sMethod
         * @return Dependency|bool
         */
        public function getInjectionClosureFor($sClassType, $sMethod)
        {
            $aDependencyCollection = $this->getDependencyMatch($sClassType, $sMethod);

            if (!empty($aDependencyCollection)) {
                $oDependency = new Dependency();
                $oDependency->setDependency($aDependencyCollection);

                return $oDependency;
            }

            // Parent class

            if ($sClassType != Dependency::CLOSURE && class_exists($sClassType)) {
                $aAncestorClasses = class_parents($sClassType);

                foreach ($aAncestorClasses as $sAncestorType) {
                    $aDependencyArray = $this->getDependencyMatchForTypeFulDependency($sAncestorType, $sMethod);
                    if (!empty($aDependencyArray)) {

                        $oDependency = new Dependency();
                        $oDependency->setDependency($aDependencyArray);

                        return $oDependency;
                    }
                }
            }

            return false;

        }

        /**
         * @param $sClassType
         * @param $sMethod
         * @return Dependency
         */
        public function getExactly($sClassType, $sMethod)
        {
            return $this->aContainer[$sClassType][$sMethod];
        }

        /**
         * @param $sClassType
         * @param $sMethod
         * @param Dependency $oDependency
         */
        public function addDependency($sClassType, $sMethod, Dependency $oDependency)
        {
            if ($this->existsExactly($sClassType, $sMethod)) {
                // We have one definition with that set of keys
                $oThisDependency = $this->getExactly($sClassType, $sMethod);
                $oThisDependency->merge($oDependency);
            } else {
                // Create new
                $this->aContainer[$sClassType][$sMethod] = $oDependency;
            }
        }

        /**
         * @param $aDependencyCollection
         * @return array
         */
        private function mergeDependencies($aDependencyCollection)
        {
            // Merge overriding older keys
            if (!empty($aDependencyCollection)) {

                /**
                 * @var $oDependency Dependency
                 */
                $oDependency = array_shift($aDependencyCollection);
                $aDependencyMerge = $oDependency->getDependencies();

                foreach ($aDependencyCollection as $oDependency) {
                    foreach ($oDependency->getDependencies() as $sDependencyClassType => $mDependency) {
                        $aDependencyMerge[$sDependencyClassType] = $mDependency;
                    }
                }
                return $aDependencyMerge;
            }

            return [];
        }

        /**
         * @param $sClassType
         * @param $sMethod
         * @return array
         */
        private function getTypeFulDependencyArray($sClassType, $sMethod)
        {
            $aDependencyCollection = [];

            // Order of ifs is IMPORTANT

            if ($this->existsExactly($sClassType, Dependency::METHOD_LESS)) {
                $aDependencyCollection[] = $this->getExactly($sClassType, Dependency::METHOD_LESS);
            }

            if ($this->existsExactly($sClassType, $sMethod)) {
                $aDependencyCollection[] = $this->getExactly($sClassType, $sMethod);
            }

            return $aDependencyCollection;
        }

        /**
         * @param $sMethod
         * @return array
         */
        private function getTypeLessDependencyArray($sMethod)
        {
            $aDependencyCollection = [];

            // Order of ifs is IMPORTANT

            if ($this->existsClosure()) {
                $aDependencyCollection = array_values($this->aContainer[Dependency::CLOSURE]);
            }

            if ($this->existsExactly(Dependency::TYPE_LESS, Dependency::METHOD_LESS)) {
                $aDependencyCollection[] = $this->aContainer[Dependency::TYPE_LESS][Dependency::METHOD_LESS];
            }

            if ($this->existsExactly(Dependency::TYPE_LESS, $sMethod)) {
                $aDependencyCollection[] = $this->aContainer[Dependency::TYPE_LESS][$sMethod];
            }

            return $aDependencyCollection;
        }

        /**
         * @param $sClassType
         * @return bool
         */
        private function isTypeLess($sClassType)
        {
            return $sClassType === Dependency::CLOSURE || $sClassType === Dependency::TYPE_LESS;
        }

    }

}
