<?php
namespace AtomPie\Boundary\System;

interface IDispatchRoutes
{
    public function dispatch($sMethod, $sShortUrl);
}