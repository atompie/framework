<?php
namespace AtomPie\Boundary\Gui\Component;

use Generi\Boundary\IType;
use AtomPie\DependencyInjection\Boundary\IConstructInjection;
use AtomPie\Boundary\Core\Dispatch\IHaveEventSpec;

interface IHaveEvents extends IHaveContext, IHavePlaceHolders, IDeliverDependencyContainer
{

    /**
     * @return IType
     */
    public function getType();

    /**
     * @return bool
     */
    public function isEventRaised();

    /**
     * @return void
     */
    public function markEventRaised();

    public function emitRequestEvent(
        IConstructInjection $oComponentDependencyContainer,
        IHaveEventSpec $oDispatchManifest
    );

}