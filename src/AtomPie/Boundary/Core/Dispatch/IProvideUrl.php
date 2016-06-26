<?php
namespace AtomPie\Boundary\Core\Dispatch;

interface IProvideUrl
{
    /**
     * Returns EndPointUrl.
     * This function is immutable and creates new EndPointEventUrl object.
     *
     * @return IAmEndPointUrl
     */
    public function getUrl();
}