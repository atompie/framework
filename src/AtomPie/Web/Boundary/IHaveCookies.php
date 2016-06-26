<?php
namespace AtomPie\Web\Boundary;

use AtomPie\Web\CookieJar;

interface IHaveCookies
{

    /**
     * @return CookieJar
     */
    public function getCookies();

    /**
     * @return bool
     */
    public function hasCookies();


}