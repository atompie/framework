<?php
namespace AtomPie\Web\Boundary;

interface IAmHttpCommunication
{

    /**
     * @return IChangeRequest
     */
    public function getRequest();

    /**
     * @return IChangeResponse
     */
    public function getResponse();
}