<?php
namespace AtomPie\Boundary\Core\Dispatch;

interface IAmEventSpecImmutable extends IHaveComponentName
{

    public function hasEvent();

    public function getEvent();

    public function getEventMethod();

    public function __toString();

    public function __toUrlString();

}