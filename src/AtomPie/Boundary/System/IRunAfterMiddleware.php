<?php
namespace AtomPie\Boundary\System;

use AtomPie\Web\Boundary\IChangeResponse;

interface IRunAfterMiddleware
{

    /**
     * @param IChangeResponse $oResponse
     * @return IChangeResponse
     */
    public function after(IChangeResponse $oResponse);

}