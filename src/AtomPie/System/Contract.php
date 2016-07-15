<?php
namespace AtomPie\System;

use AtomPie\Boundary\System\IAmContractFiller;

class Contract implements IAmContractFiller
{

    private $sInterfaceClassName;
    private $sClassName;
    private $sMethodName;
    private $sContractFillerClassName;

    /**
     * Contract constructor.
     * @param $sInterfaceClassName
     */
    public function __construct($sInterfaceClassName)
    {
        $this->sInterfaceClassName = $sInterfaceClassName;
    }

    /**
     * @param $sClassName
     * @param $sMethodName
     * @return $this
     */
    public function forClassAndMethod($sClassName, $sMethodName)
    {
        $this->sClassName = $sClassName;
        $this->sMethodName = $sMethodName;
        return $this;
    }

    /**
     * @param $sClassName
     * @return $this
     */
    public function forClass($sClassName)
    {
        $this->sClassName = $sClassName;
        $this->sMethodName = null;
        return $this;
    }

    /**
     * @param $sContractFillerClassName
     * @return $this
     */
    public function fillBy($sContractFillerClassName)
    {
        $this->sContractFillerClassName = $sContractFillerClassName;
        return $this;
    }

    /**
     * @param $sClassName
     * @param $sMethodName
     * @return bool
     */
    public function isConstrainedToClassAndMethod($sClassName, $sMethodName)
    {
        if (isset($this->sMethodName) && isset($this->sClassName)) {
            return $sClassName == $this->sClassName && $sMethodName == $this->sMethodName;
        }

        return false;
    }

    /**
     * @param $sClassName
     * @return bool
     */
    public function isConstrainedToClass($sClassName)
    {
        if (isset($this->sClassName)) {
            return $sClassName == $this->sClassName;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isGlobal() {
        return !isset($this->sClassName);
    }

    /**
     * @param $sInterfaceClassName
     * @return bool
     */
    public function isForContract($sInterfaceClassName)
    {
        return $this->sInterfaceClassName == $sInterfaceClassName;
    }

    /**
     * @return string
     */
    public function getContractFiller()
    {
        return $this->sContractFillerClassName;
    }
}
