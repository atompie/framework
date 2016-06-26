<?php
namespace AtomPie\Boundary\Gui\Component;

use AtomPie\Web\Boundary\IAmRequestParam;

interface IAmComponentParam extends IAmRequestParam
{
    /**
     * @param string $sComponentContext
     */
    public function setComponentContext($sComponentContext);

    /**
     * @return string
     */
    public function getComponentContext();

    /**
     * @return bool
     */
    public function hasComponentContext();
}