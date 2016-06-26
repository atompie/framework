<?php
namespace AtomPie\Web\Boundary;

interface IAmImmutableHttpData
    extends IHaveContent,
    IHaveCookies,
    IHaveHeaders
{

    /**
     * @return string $sRequestString
     */
    public function getRequestString();

}