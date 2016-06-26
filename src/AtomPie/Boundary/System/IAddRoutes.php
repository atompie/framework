<?php
namespace AtomPie\Boundary\System;

interface IAddRoutes
{
    public function addRoute($aMethods, $sRoute, $mHandler);
}