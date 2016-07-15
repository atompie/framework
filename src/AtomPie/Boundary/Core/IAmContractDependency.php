<?php
namespace AtomPie\Boundary\Core;

interface IAmContractDependency
{
    /**
     * Returns array of services that will fill 
     * the contract/interface in form of
     * 
     * [
     *     Interface::class => Service::class
     * ]
     *
     * @return array
     */
    public function provideContractDependencies();
}