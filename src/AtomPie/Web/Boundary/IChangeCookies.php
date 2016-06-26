<?php
namespace AtomPie\Web\Boundary;

use AtomPie\Web\Cookie;
use AtomPie\Web\CookieJar;

interface IChangeCookies extends IHaveCookies
{
    /**
     * @param Cookie $oCookie
     */
    public function addCookie(Cookie $oCookie);

    /**
     * @param \AtomPie\Web\CookieJar $oMyCookieJar
     */
    public function appendCookieJar(CookieJar $oMyCookieJar);
}