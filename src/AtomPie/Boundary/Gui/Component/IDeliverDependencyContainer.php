<?php
namespace AtomPie\Boundary\Gui\Component;

use AtomPie\DependencyInjection\Boundary\IConstructInjection;

interface IDeliverDependencyContainer
{
    /**
     * @return IConstructInjection
     */
    public function getComponentDependencyContainer();
}