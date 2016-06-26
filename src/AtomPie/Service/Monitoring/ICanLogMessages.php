<?php
namespace AtomPie\Service\Monitoring;

interface ICanLogMessages
{
    public function log(IAmProfileMessage $oLog, $bDeferredPersistence = false);
}