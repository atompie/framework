<?php
namespace AtomPie\Boundary\Gui\Component;

use AtomPie\Web\Boundary\IChangeRequest;
use AtomPie\Web\Boundary\IChangeResponse;

interface IControlDispatch
{

    /**
     * Triggered if component raise event.
     *
     * @param IChangeRequest $oRequest
     */
    public function __onBefore(IChangeRequest $oRequest);

    /**
     * Triggered if component raise event.
     *
     * @param $mReturn
     * @return void
     */
    public function __onAfter($mReturn);

    /**
     * Response is filled.
     *
     * @param IChangeResponse $oResponse
     * @return IChangeResponse
     */
    public function __onResponse(IChangeResponse $oResponse);

}