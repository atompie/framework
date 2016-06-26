<?php
namespace AtomPie\Boundary\Core\Dispatch;

use Generi\Boundary\IType;

interface IHaveComponentName
{

    /**
     * @return string
     */
    public function getComponentName();

    /**
     * @return IType
     */
    public function getComponentType();

}