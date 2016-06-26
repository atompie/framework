<?php
namespace AtomPie\Web\Boundary;

use AtomPie\Web\Connection\Http\Url;

interface IAmImmutableUrl
{
    /**
     * Returns TRUE if request has set URL, FALSE if oposit.
     *
     * @return bool
     */
    public function hasUrl();

    /**
     * Returns \Web\Connection\Http\Url.
     *
     * @return Url $oHttpUrl
     */
    public function getUrl();

    public function getAnchor();

    public function hasAnchor();

    /**
     * Returns \Web\Connection\Http\Url parameters as array.
     *
     * @return array $aParams
     */
    public function getParams();

    /**
     * Returns true if \Web\Connection\Http\Url has parameters.
     *
     * @return bool
     */
    public function hasParams();

    /**
     * @param string $sParamName
     * @return string
     */
    public function getParam($sParamName);

    /**
     * Returns $aParams as key1=value1&key2=value
     *
     * @param IChangeRequest | array $mParams
     * @return string
     */
    public static function getParamsAsString($mParams);

    /**
     * Returns url as a string. Without server address.
     *
     * @return string|NULL
     */
    public function getRequestString();
}