<?php
namespace AtomPie\Boundary\Core;

interface IRegisterContentProcessors
{
    public function registerAfter($sReturnType, $pClosure);

    public function registerBefore($pClosure);

    public function registerFinally($sReturnType, $pClosure);
}