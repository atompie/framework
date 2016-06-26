<?php
namespace AtomPie\Web\Boundary;

interface IAmHttpHeader extends IAmHeader
{
    /**
     * Sets value of a header.
     *
     * @param $sValue
     */
    public function setValue($sValue);

}