<?php
namespace AtomPie\Boundary\Gui\Component;

interface IAmEventUrl
{

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @return array
     */
    public function getParams();

    /**
     * Adds param to url
     *
     * @param $sName
     * @param $sValue
     */
    public function addParam($sName, $sValue);

    /**
     * Removes param from url
     *
     * @param $sName
     */
    public function removeParam($sName);

    /**
     * @param $aParams
     */
    public function setParams($aParams);

    /**
     * @return string
     */
    public function getEvent();

    public function __toString();
}