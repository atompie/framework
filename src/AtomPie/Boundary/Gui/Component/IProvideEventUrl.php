<?php
namespace AtomPie\Boundary\Gui\Component;

use AtomPie\Boundary\Core\Dispatch\IProvideUrl;

interface IProvideEventUrl extends IProvideUrl
{

    /**
     * Returns event url within the context of current EndPoint.
     *
     * @param IHaveEvents $oComponent
     * @param $sEvent
     * @param array $aParams
     * @return IAmEventUrl
     */
    public function getEventUrl(IHaveEvents $oComponent, $sEvent, array $aParams = null);

}