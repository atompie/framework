<?php
namespace AtomPie\Web\Boundary;

interface IChangeStatusHeader extends IHaveStatusHeader
{
    /**
     * Sets status code.
     *
     * @param IAmStatusHeader $oStatus
     */
    public function setStatus(IAmStatusHeader $oStatus);
}