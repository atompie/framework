<?php

namespace AtomPie\Web\Boundary;

interface IChangeContent extends IHaveContent
{
    /**
     * Sets content.
     *
     * @param IAmContent $oContent
     */
    public function setContent(IAmContent $oContent);

    public function setContentType($sContentType);

}