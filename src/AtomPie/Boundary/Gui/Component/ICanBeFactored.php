<?php
namespace AtomPie\Boundary\Gui\Component;

use Generi\Boundary\IObject;

interface ICanBeFactored extends IObject, IDeliverDependencyContainer
{
    /**
     * @internal
     * @return void
     */
    public function markFactored();

    /**
     * @internal
     * @return bool
     */
    public function isFactored();

}