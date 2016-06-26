<?php
namespace AtomPie\DependencyInjection\Boundary;

interface IDefineDependency
{

    /**
     * @param array $aDependencies
     */
    public function setDependency(array $aDependencies);

    /**
     * @param array $aDependencies
     */
    public function addDependency(array $aDependencies);

    /**
     * @param array $aDependencies
     */
    public function replaceDependency(array $aDependencies);

    /**
     * @return array
     */
    public function getDependencies();

    public function hasTypeLessDependency();

    /**
     * @return \Closure
     */
    public function getTypeLessDependency();

    /**
     * @param IDefineDependency $oDependency
     */
    public function merge(IDefineDependency $oDependency);
}