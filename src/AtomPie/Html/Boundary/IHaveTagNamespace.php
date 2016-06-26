<?php
namespace AtomPie\Html\Boundary;

interface IHaveTagNamespace
{

    public function getNamespace();

    public function setNamespace($sNamespace);

    public function hasNamespace();

}