<?php
namespace AtomPie\Web\Boundary;

interface IAmUrl extends IAmImmutableUrl
{

    public function setAnchor($sAnchor = null);

    /**
     * Sets url parameters. Pass array of key, value to set parameters.
     *
     * @param array $aParams
     */
    public function setParams($aParams);

    /**
     * Sets url.
     *
     * @param string $sUrl
     */
    public function setUrl($sUrl);

    /**
     * Adds key, value pair as url parameters.
     *
     * @param string $sKey
     * @param string $sValue
     */
    public function addKeyValueParam($sKey, $sValue);

    /**
     * Adds url parameter as Web\Connection\Http\Url\Param class.
     *
     * @param IAmRequestParam $oParam
     */
    public function addHttpParam(IAmRequestParam $oParam);

    /**
     * Removes parameter from Web\Connection\Http\Url
     *
     * @param string $sKey
     */
    public function removeParam($sKey);
}