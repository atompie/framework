<?php

namespace AtomPie\Boundary\Gui\Component;

interface IBasicAuthorize
{
    public function checkAuthorizeAnnotation($oEndPointObject, $sEndPointMethod = null);
}