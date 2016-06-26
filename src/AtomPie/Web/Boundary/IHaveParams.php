<?php
namespace AtomPie\Web\Boundary;

interface IHaveParams
{

    /**
     * @param null $sMethod
     * @return \Generi\Boundary\ICollection | null
     */
    public function getAllParams($sMethod = null);

    /**
     * Returns request parameter.
     *
     * @param string $sName
     * @param null $sMethod
     * @return string | null
     */
    public function getParam($sName, $sMethod = null);

    /**
     * @param string $sName
     * @param string $sMethod
     * @return bool
     */
    public function hasParam($sName, $sMethod = null);

}