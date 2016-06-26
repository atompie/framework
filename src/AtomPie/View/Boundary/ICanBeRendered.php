<?php
namespace AtomPie\View\Boundary;

interface ICanBeRendered extends IHaveTemplate
{
    /**
     * @return array
     */
    public function getViewPlaceHolders();
}