<?php
namespace AtomPie\DependencyInjection\Boundary;

interface IAmDependencyMetaData
{
    /**
     * @return mixed
     */
    public function getCalledMethod();

    /**
     * @return object
     */
    public function getObject();

    /**
     * @return mixed
     */
    public function getCalledClassType();

    /**
     * @return \ReflectionParameter
     */
    public function getParamMetaData();

    /**
     * @return \ReflectionFunctionAbstract
     */
    public function getCalledFunctionMetaData();

    /**
     * @return mixed
     */
    public function isClass();
}