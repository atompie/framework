<?php
namespace AtomPie\Web\Boundary;

use AtomPie\Web\Connection\Http\UploadFile;
use AtomPie\Web\Connection\ProxyCredentials;

interface IChangeRequest
    extends IChangeHttpMethod,
    IChangeUrl,
    IChangeHeaders,
    IChangeTimeOut,
    IChangeParams,
    IAmRequest
{

    ////////////////////////
    // I Handle referrer

    /**
     * @return string
     */
    public function getReferrerUrl();

    /**
     * @param string $sReferrerUrl
     */
    public function setReferrerUrl($sReferrerUrl);

    /**
     * @return bool
     */
    public function hasReferrerUrl();

    ////////////////////////
    // I Handle proxy

    public function removeProxy();

    /**
     * @return bool
     */
    public function hasProxy();

    /**
     * @return bool
     */
    public function hasProxyCredentials();

    /**
     * @return ProxyCredentials
     */
    public function getProxyCredentials();

    /**
     * @return string
     */
    public function getProxy();

    /**
     * @param string $sProxy
     * @param ProxyCredentials $oCredentials
     */
    public function setProxy($sProxy, ProxyCredentials $oCredentials = null);

    ////////////////////////
    // I Handle files

    /**
     * @param $sParamName
     * @return bool
     */
    public function hasUploadedFile($sParamName);

    /**
     * @param $sParamName
     * @return UploadFile
     */
    public function getFile($sParamName);

    ////////////////////////
    //

    /**
     * Loads data from received request.
     *
     * @return $this
     */
    public function load();

    public function send($sUrl = null, $aParams = null);

}