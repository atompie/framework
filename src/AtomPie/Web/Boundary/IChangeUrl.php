<?php
namespace AtomPie\Web\Boundary;

use AtomPie\Web\Connection\Http\Url;

interface IChangeUrl extends IHaveUrl
{
    /**
     * Sets \Web\Connection\Http\Url.
     *
     * @param Url $oHttpUrl
     */
    public function setUrl(Url $oHttpUrl);
}