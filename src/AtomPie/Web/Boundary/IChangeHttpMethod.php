<?php
namespace AtomPie\Web\Boundary;

interface IChangeHttpMethod extends IHaveHttpMethod
{

    /**
     * Sets method. use \Web\Connection\Http\Request::GET,
     *                    \Web\Connection\Http\Request::POST,
     *                    \Web\Connection\Http\Request::PUT,
     *                    \Web\Connection\Http\Request::DELETE
     *
     * @param string $sMethod
     */
    public function setMethod($sMethod);

}