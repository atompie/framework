<?php
namespace AtomPie\Web\Boundary;

interface IAbstractServer
{
    public function ContentType();

    public function QueryString();

    public function RemoteAddress();

    public function RequestMethod();
}