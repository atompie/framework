<?php
namespace AtomPie\Annotation;

// TODO remove not used methods
use Generi\Boundary\IAmNameValuePairImmutable;

class Attribute implements IAmNameValuePairImmutable
{

    private $sNamespace;
    private $sName;
    private $aValues = array();
    private $bUrlEncode = true;

    /**
     * Holds node type.
     *
     * @var int
     */
    public $iNodeType;

    const XML_TEXT_NODE = 3;

    public function __construct($sName, $sValue, $sNamespace = null)
    {
        $this->sName = (string)$sName;
        $this->aValues[(string)$sValue] = $sValue;
        $this->sNamespace = $sNamespace;
        $this->iNodeType = self::XML_TEXT_NODE;
    }

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