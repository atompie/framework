<?php
namespace AtomPie\Web\Boundary;

interface IHaveHttpMethod
{

    /**
     * Returns method.
     *
     * @return string
     */
    public function getMethod();

    /**
     * Returns TRUE if method is set, false if oposit.
     *
     * @param string $sMethod use
     *                    \Web\Connection\Http\Request::GET,
     *                    \Web\Connection\Http\Request::POST,
     *                    \Web\Connection\Http\Request::PUT,
     *                    \Web\Connection\Http\Request::DELETE
     *                    instead of string
     *
     * @return boolean
     */
    public function isMethod($sMethod);

}