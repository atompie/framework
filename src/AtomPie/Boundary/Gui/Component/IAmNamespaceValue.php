<?php
namespace AtomPie\Boundary\Gui\Component;

use Generi\Boundary\IHaveName;

interface IAmNamespaceValue extends IHaveName
{
    /**
     * @return string
     */
    public function getNamespace();

    /**
     * @param string $sNamespace
     */
    public function setNamespace($sNamespace);

    /**
     * @return bool
     */
    public function hasNamespace();

    /**
     * @param string $sName
     */
    public function setName($sName);

    /**
     * @return string
     */
    public function __toString();

}