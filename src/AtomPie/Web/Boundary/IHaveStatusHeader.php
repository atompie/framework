<?php
namespace AtomPie\Web\Boundary;

interface IHaveStatusHeader
{
    /**
     * Returns status code.
     *
     * @return IAmStatusHeader
     */
    public function getStatus();

}