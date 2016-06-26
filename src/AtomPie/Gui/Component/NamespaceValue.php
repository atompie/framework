<?php
namespace AtomPie\Gui\Component {

    use AtomPie\Boundary\Gui\Component\IAmNamespaceValue;

    class NamespaceValue implements IAmNamespaceValue
    {

        const NAMESPACE_SEPARATOR = '\\';

        /**
         * @var string
         */
        private $sNamespace;

        /**
         * @var string
         */
        private $sName;

        public function __construct($sNamespace, $sName)
        {
            $this->sNamespace = $sNamespace;
            $this->sName = $sName;
        }

        /**
         * @return string
         */
        public function getNamespace()
        {
            return $this->sNamespace;
        }

        /**
         * @param string $sNamespace
         */
        public function setNamespace($sNamespace)
        {
            $this->sNamespace = $sNamespace;
        }

        /**
         * @return bool
         */
        public function hasNamespace()
        {
            return !empty($this->sNamespace);
        }

        /**
         * @return string
         */
        public function getName()
        {
            return $this->sName;
        }

        /**
         * @param string $sName
         */
        public function setName($sName)
        {
            $this->sName = $sName;
        }

        /**
         * @return bool
         */
        public function hasName()
        {
            return !empty($this->sName);
        }

        public function __toString()
        {
            if ($this->hasNamespace()) {
                return $this->getNamespace() . self::NAMESPACE_SEPARATOR . $this->getName();
            }
            return $this->getName();
        }

    }

}