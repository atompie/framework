<?php
namespace AtomPie\Web\Boundary;

use AtomPie\Web\Connection\Http\Url;

interface IHaveUrl
{
    /**
     * Returns TRUE if request has set URL, FALSE if oposit.
     *
     * @return bool
     */
    public function hasUrl();

    /**
     * Returns \Web\Connection\Http\Url.
     *
     * @return Url $oHttpUrl
     */
    public function getUrl();
}