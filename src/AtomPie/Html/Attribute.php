<?php
namespace AtomPie\Html {

    use AtomPie\Html\Boundary\IAmAttribute;

    class Attribute extends Node implements IAmAttribute
    {

        private $sNamespace;
        private $sName;
        private $aValues = array();
        private $bUrlEncode = true;

        public function __construct($sName, $sValue, $sNamespace = null)
        {
            $this->sName = (string)$sName;
            $this->aValues[(string)$sValue] = $sValue;
            $this->sNamespace = $sNamespace;
            $this->iNodeType = self::XML_TEXT_NODE;
        }

        ///////////////////////////
        // \Html\ITagNamespace

        final public function getNamespace()
        {
            return $this->sNamespace;
        }

        final public function setNamespace($sNamespace)
        {
            $this->sNamespace = $sNamespace;
        }

        final public function hasNamespace()
        {
            return !empty($this->sNamespace);
        }

        ///////////////////////////
        // IAmAttribute

        final public function setValue($sValue)
        {
            $this->aValues = array($sValue => $sValue);
        }

        final public function getValue()
        {
            return implode(' ', $this->aValues);
        }

        final public function notEmpty()
        {
            return !empty($this->aValues);
        }

        final public function addValue($sValue)
        {
            $this->aValues[$sValue] = $sValue;
        }

        final public function removeValue($sValue)
        {
            unset($this->aValues[$sValue]);
        }

        final public function setName($sName)
        {
            // None utf8 compliant
            $this->sName = strtolower($sName);
        }

        final public function getName()
        {
            return $this->sName;
        }

        final public function hasValue($sValue)
        {
            return in_array($sValue, $this->aValues);
        }

        final public function encode($bFlag)
        {
            $this->bUrlEncode = $bFlag;
        }

        public function __toString()
        {
            if ($this->hasNamespace()) {
                return $this->sNamespace . ':' . $this->renderAttribute();
            }
            return $this->renderAttribute();
        }

        ////////////////////////////

        private function renderAttribute()
        {
            return $this->getName() . '="' .
            (
            ($this->bUrlEncode)
                ? htmlspecialchars($this->getValue(), ENT_COMPAT, 'UTF-8')
                : $this->getValue()
            ) . '"';
        }


    }
}