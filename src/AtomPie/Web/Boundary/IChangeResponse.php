<?php
namespace AtomPie\Web\Boundary;

interface IChangeResponse extends
    IChangeStatusHeader,
    IChangeCookies,
    IChangeHeaders,
    IChangeContent
{

    /**
     * Sends response.
     */
    public function send();

}