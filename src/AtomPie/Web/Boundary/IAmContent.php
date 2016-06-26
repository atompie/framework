<?php
namespace AtomPie\Web\Boundary;

interface IAmContent
{

    /**
     * @return bool
     */
    public function isFile();

    /**
     * Returns content as a string.
     *
     * @return string
     */
    public function __toString();

    /**
     * Content length.
     *
     * @return int $iContentLength
     */
    public function getContentLength();

    /**
     * Content type.
     *
     * @return IAmContentType
     */
    public function getContentType();

    /**
     * @return bool
     */
    public function hasContentType();

    /**
     * Return content
     *
     * @return string
     */
    public function get();

    /**
     * Returns true if content is not empty and false if empty.
     *
     * @return boolean
     */
    public function notEmpty();

    /**
     * Sets content.
     *
     * @param string $sContent
     * @param IAmContentType $oContentType
     */
    public function set($sContent, IAmContentType $oContentType = null);

    /**
     * @param $sContent
     */
    public function setContent($sContent);

    /**
     * @param IAmContentType $oContentType
     */
    public function setContentType(IAmContentType $oContentType);

    /**
     * Decodes json content.
     *
     * @param bool $bAssoc [optional]
     * When true, returned objects will be converted into
     * associative arrays.
     *
     * @return mixed the value encoded in json in appropriate
     * PHP type. Values true, false and
     * null (case-insensitive) are returned as true, false
     * and NULL respectively. NULL is returned if the
     * json cannot be decoded or if the encoded
     * data is deeper than the recursion limit.
     */
    public function decodeAsJson($bAssoc = null);

    /**
     * Returns the JSON representation of a value
     * @link http://www.php.net/manual/en/function.json-encode.php
     *
     * @param options int[optional] <p>
     * Bitmask consisting of JSON_HEX_QUOT,
     * JSON_HEX_TAG,
     * JSON_HEX_AMP,
     * JSON_HEX_APOS,
     * JSON_FORCE_OBJECT.
     * </p>
     * @return string a JSON encoded string on success.
     */
    public function encodeAsJson($iOptions = null);

    /**
     * @return \SimpleXMLElement
     */
    public function getAsSimpleXml();
}