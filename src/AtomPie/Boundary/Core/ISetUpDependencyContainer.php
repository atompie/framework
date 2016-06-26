<?php
namespace AtomPie\Boundary\Core;

interface ISetUpDependencyContainer
{
    /**
     * @param \Closure[] $aDependencySet
     */
    public function initDependencyContainer($aDependencySet);
}