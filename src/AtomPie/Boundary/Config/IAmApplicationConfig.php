<?php
namespace AtomPie\Boundary\Config;

/**
 * Class Config
 */
interface IAmApplicationConfig
{
    public function __get($sName);

    /**
     * Return true if any value is set. Null is also value.
     *
     * @param $sName
     * @return bool
     */
    public function __isset($sName);
}