<?php
namespace AtomPie\Web\Boundary;

interface IChangeParams extends IHaveParams
{
    /**
     * @param $sKey
     */
    public function removeParam($sKey);

    /**
     * @param string $sKey
     * @param string $sValue
     * @param null $sMethod
     */
    public function setParam($sKey, $sValue, $sMethod = null);

}