<?php
namespace AtomPie\View\Boundary;

use AtomPie\System\IO\File;

interface IHaveTemplate
{
    /**
     * @param $sFolder
     * @return File
     */
    public function getTemplateFile($sFolder);
}