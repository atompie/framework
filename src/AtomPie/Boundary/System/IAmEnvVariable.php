<?php
namespace AtomPie\Boundary\System;

interface IAmEnvVariable
{
    /**
     * Returns environment variable
     * 
     * @param $sName
     * @return string
     */
    public function get($sName);

    /**
     * 
     * Returns true if environment variable exists. False otherwise. 
     * @param $sName
     * @return bool
     */
    public function has($sName);
}