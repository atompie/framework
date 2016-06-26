<?php
namespace AtomPie\Boundary\System;

use AtomPie\Boundary\Core\Dispatch\IAmDispatchManifest;
use AtomPie\Boundary\Core\Dispatch\IAmEndPointValue;
use AtomPie\Boundary\Core\IProcessContent;
use AtomPie\Boundary\Core\IAmFrameworkConfig;
use AtomPie\DependencyInjection\Boundary\IConstructInjection;
use AtomPie\Web\Boundary\IAmEnvironment;

interface IAmDispatcher
{

    public function dispatch(
        $oEndPointObject,
        IAmFrameworkConfig $oConfig,
        IAmEnvironment $oEnvironment,
        IProcessContent $oContentProcessor,
        IConstructInjection $oEndPointDependencyContainer
    );

    public function getEndPointObject(
        $sEndPointFullClassName,
        IAmFrameworkConfig $oConfig,
        IAmEndPointValue $oEndPointSpec
    );

    /**
     * @return IAmDispatchManifest
     */
    public function getDispatchManifest();

}