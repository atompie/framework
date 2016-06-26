<?php
namespace AtomPie\Core {

    class NamespaceHandler
    {

        /**
         * @var array
         */
        private $aNamespaceShortCuts;
        /**
         * @var array
         */
        private $aClassShortCuts;

        public function __construct(array $aNamespaceShortCuts = null, array $aClassShortCuts = null)
        {
            $this->aNamespaceShortCuts = $aNamespaceShortCuts;
            $this->aClassShortCuts = $aClassShortCuts;
        }

        public function shorten($sClassName, $bNoLeadingBackSlash = true)
        {

            if (!empty($this->aClassShortCuts)) {
                foreach ($this->aClassShortCuts as $sFullClassName) {
                    // Full class name
                    if ($sFullClassName == $sClassName) {
                        $aNamespaceChunks = explode('\\', $sClassName);
                        return array_pop($aNamespaceChunks);
                    }
                }
            }

            if (!empty($this->aNamespaceShortCuts)) {

                foreach ($this->aNamespaceShortCuts as $sNamespaceShortCut) {
                    if ($sClassName{0} == '\\' && $sNamespaceShortCut{0} != '\\') {
                        $sNamespaceShortCut = '\\' . $sNamespaceShortCut;
                    } else {
                        if ($sClassName{0} != '\\' && $sNamespaceShortCut{0} == '\\') {
                            $sNamespaceShortCut = substr($sNamespaceShortCut, 1);
                        }
                    }

                    if ($this->startsWith($sNamespaceShortCut, $sClassName)) {
                        $sShortClassName = substr($sClassName, strlen($sNamespaceShortCut));
                        if ($bNoLeadingBackSlash) {
                            return trim($sShortClassName, '\\');
                        }

                        return $sShortClassName;
                    }
                }
            }

            if ($bNoLeadingBackSlash) {
                return trim($sClassName, '\\');
            }

            return $sClassName;

        }

        public function getFullClassName($sClassName)
        {

            if (!empty($this->aClassShortCuts)) {
                foreach ($this->aClassShortCuts as $sFullClassName) {
                    $aClassNamespaceChunks = explode('\\', $sFullClassName);
                    $sShortClassName = array_pop($aClassNamespaceChunks);
                    // Full class name
                    if ($sShortClassName == $sClassName) {
                        if (class_exists($sFullClassName)) {
                            return $sFullClassName;
                        }
                    }
                }
            }

            if (!empty($this->aNamespaceShortCuts)) {
                foreach ($this->aNamespaceShortCuts as $sNamespace) {
                    $sClass = $this->appendNamespaceToClass($sNamespace, $sClassName);
                    if (class_exists($sClass)) {
                        return $sClass;
                    }
//                list($sClass,$sClassNamespace) = $this->mergeNamespaceToClass($sNamespace ,$sClassName);
//                if(class_exists($sClass)) {
//                    return $sClass;
//                }
                }
            }
            return null;
        }

        private function appendNamespaceToClass($sNamespace, $sClassName)
        {
            return trim($sNamespace, '\\') . '\\' . $sClassName;
        }

        public function mergeNamespaceToClass($sNamespace, $sClassName)
        {

            $sClassName = trim($sClassName, '\\');

            $aClassChunks = explode('\\', $sClassName);

            $aExplodedClass = array();

            $aExplodedClass['class'] = array_pop($aClassChunks);
            $aExplodedClass['namespace'] = implode('\\', $aClassChunks);
            $iCut = -strlen($aExplodedClass['namespace']) - 1;
            $sNamespaceSuffix = substr($sNamespace, $iCut);
            if ($sNamespaceSuffix == '\\' . $aExplodedClass['namespace']) {
                return array($sNamespace . '\\' . $aExplodedClass['class'], substr($sNamespace, 0, $iCut));
            }
            return array($sClassName, $sNamespace);
        }

        /**
         * @param $sClassName
         * @return string | null
         */
        public function getNamespaceForClass($sClassName)
        {

            if (!empty($this->aClassShortCuts)) {
                foreach ($this->aClassShortCuts as $sFullClassName) {
                    $aClassNamespaceChunks = explode('\\', $sFullClassName);
                    $sShortClassName = array_pop($aClassNamespaceChunks);
                    // Full class name
                    if ($sShortClassName == $sClassName) {
                        if (class_exists($sFullClassName)) {
                            return implode('\\', $aClassNamespaceChunks);
                        }
                    }
                }
            }

            if (!empty($this->aNamespaceShortCuts)) {
                foreach ($this->aNamespaceShortCuts as $sNamespace) {
                    $sClass = $this->appendNamespaceToClass($sNamespace, $sClassName);
                    if (class_exists($sClass)) {
                        return $sNamespace;
                    }
                    list($sClass, $sClassNamespace) = $this->mergeNamespaceToClass($sNamespace, $sClassName);
                    if (class_exists($sClass)) {
                        return $sClassNamespace;
                    }
                }
            }

            return null;
        }


        private function startsWith($sNamespaceShortCut, $sNamespace)
        {
            return $sNamespaceShortCut == substr($sNamespace, 0, strlen($sNamespaceShortCut));
        }
    }

}
