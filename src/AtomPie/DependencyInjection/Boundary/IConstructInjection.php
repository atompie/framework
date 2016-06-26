<?php
namespace AtomPie\DependencyInjection\Boundary;

use AtomPie\DependencyInjection\Dependency;

interface IConstructInjection
{
    /**
     * @param $sClassType
     * @param $sMethod
     * @return Dependency|bool
     */
    public function getInjectionClosureFor($sClassType, $sMethod);
}
