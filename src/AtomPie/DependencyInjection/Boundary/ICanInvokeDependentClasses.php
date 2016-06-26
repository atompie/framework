<?php
namespace AtomPie\DependencyInjection\Boundary;

interface ICanInvokeDependentClasses
{
    /**
     * Invokes closure defined as $sClosureId
     *
     * @param $sClosureId
     * @param \Closure $pClosure
     * @return mixed
     */
    public function invokeClosure($sClosureId, \Closure $pClosure);

    /**
     * Invokes $sMethod in $oObject
     *
     * @param $oObject
     * @param $sMethod
     * @return mixed
     */
    public function invokeMethod($oObject, $sMethod);

}