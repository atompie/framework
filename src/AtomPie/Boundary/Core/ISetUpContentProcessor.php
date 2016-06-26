<?php
namespace AtomPie\Boundary\Core;

use AtomPie\Boundary\Core\Dispatch\IAmDispatchManifest;

interface ISetUpContentProcessor
{

    public function init(IAmDispatchManifest $oDispatchManifest);

    public function configureProcessor(IRegisterContentProcessors $oContentProcessor);
}