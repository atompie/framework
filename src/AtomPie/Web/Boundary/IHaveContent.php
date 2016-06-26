<?php

namespace AtomPie\Web\Boundary;

interface IHaveContent
{

    /**
     * Returns content.
     *
     * @return IAmContent
     */
    public function getContent();

    /**
     * Returns TRUE if HTTP communication has content.
     *
     * @return bool
     */
    public function hasContent();


}