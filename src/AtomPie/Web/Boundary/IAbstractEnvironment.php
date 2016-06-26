<?php
namespace AtomPie\Web\Boundary;

interface IAbstractEnvironment
{
    public function deliverGet();

    public function deliverPost();

    public function deliverRequest();

    public function deliverServer();

    public function deliverCookies();

    public function deliverHeaders();

    public function deliverRawContent();
}