<?php
namespace AtomPie\Boundary\System;

interface IAmContractFiller
{
    /**
     * @param $sClassName
     * @param $sMethodName
     * @return bool
     */
    public function isConstrainedToClassAndMethod($sClassName, $sMethodName);

    /**
     * @param $sClassName
     * @return bool
     */
    public function isConstrainedToClass($sClassName);

    /**
     * @return bool
     */
    public function isGlobal();

    /**
     * @param $sInterfaceClassName
     * @return bool
     */
    public function isForContract($sInterfaceClassName);

    /**
     * @return string
     */
    public function getContractFiller();
}