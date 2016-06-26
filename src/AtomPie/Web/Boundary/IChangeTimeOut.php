<?php
namespace AtomPie\Web\Boundary;

interface IChangeTimeOut extends IHaveTimeOut
{
    /**
     * Sets request time out.
     *
     * @param int $iTimeOut
     */
    public function setTimeOut($iTimeOut);

}