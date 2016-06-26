<?php
namespace AtomPie\Boundary\Core\Dispatch;

interface IHaveEventSpec
{
    /**
     * @return bool
     */
    public function hasEventSpec();

    /**
     * @return IAmEventSpecImmutable
     */
    public function getEventSpec();
}