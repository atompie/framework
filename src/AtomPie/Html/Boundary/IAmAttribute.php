<?php
namespace AtomPie\Html\Boundary;

use Generi\Boundary\IAmNameValuePair;

interface IAmAttribute extends IHaveTagNamespace, IAmNameValuePair
{
    public function addValue($sValue);
    public function removeValue($sValue);
    public function notEmpty();
    public function encode($bFlag);
    public function __toString();
}