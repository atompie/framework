<?php
namespace AtomPie\Boundary\Core\Dispatch;

interface IHaveAccessToEndPoint
{

    /**
     * Returns instance of EndPointUrl filled with EndPoint string.
     *
     * @return IAmEndPointUrl
     */
    public function cloneEndPointUrl();

}