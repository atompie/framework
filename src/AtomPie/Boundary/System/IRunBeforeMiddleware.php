<?php
namespace AtomPie\Boundary\System;

use AtomPie\Web\Boundary\IChangeRequest;
use AtomPie\Web\Boundary\IChangeResponse;

interface IRunBeforeMiddleware
{
    /**
     * Returns modified Request.
     *
     * @param IChangeRequest $oRequest
     * @param IChangeResponse $oResponse
     * @return IChangeRequest
     */
    public function before(IChangeRequest $oRequest, IChangeResponse $oResponse);

}