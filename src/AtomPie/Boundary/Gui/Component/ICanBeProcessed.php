<?php

namespace AtomPie\Boundary\Gui\Component;

/**
 * Defines is class can be processed as component
 */
interface ICanBeProcessed extends IDeliverDependencyContainer
{
    public function markProcessed();

    public function isProcessed();
}