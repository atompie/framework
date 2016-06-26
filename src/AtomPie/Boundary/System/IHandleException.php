<?php
namespace AtomPie\Boundary\System;

use AtomPie\Web\Boundary\IRecognizeMediaType;

interface IHandleException
{
    public function handleException(\Exception $oException, IRecognizeMediaType $oContentType);
}