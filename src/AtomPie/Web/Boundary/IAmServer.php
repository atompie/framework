<?php
namespace AtomPie\Web\Boundary;

interface IAmServer
{

    public function getServerUri();

    public function getServerUrl($sUrl = null, $bWithPort = true);

    public function getServer($bWithPort = true);

    public function getServerName();

    public function getServerPhpFolder();

    /**
     * @return string | null
     */
    public function getSelfPhpFile();

    /**
     * @return string
     */
    public function getSelfPhpFolder();

    public function getRequestUri();

    public function getDocumentRoot();

    public function getContextDocumentRoot();

    public function getScriptName();

    public function getScriptFileName();

    public function getServerAdmin();

    public function getRequestScheme();

    public function getProtocol();

    public function getRemotePort();

    public function getRequestMethod();

    public function getQueryString();

    public function getIp();

    public function isLocalHost();

    /**
     * @return bool
     */
    public function isHttps();

    /**
     * @return mixed
     */
    public function getHost();

    /**
     * @return mixed
     */
    public function getPort();
}