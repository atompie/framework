<?php
namespace AtomPie\Web\Boundary;

interface IRecognizeMediaType
{

    /**
     * Returns true if Content-Type header indicates that
     * it has Json encoded content.
     *
     * Also check if content can be json_decoded, as Content-Type
     * header is only information on content type and can be
     * wrong if not implemented correctly.
     *
     * @return boolean
     */
    public function isJson();

    /**
     * Returns true if Content-Type header indicates that
     * it has Xml content.
     *
     * @return boolean
     */
    public function isXml();

    /**
     * Returns true if Content-Type header indicates that
     * it has Text content.
     *
     * @return boolean
     */
    public function isText();

    /**
     * Returns true if Content-Type header indicates that
     * it has Javascript content.
     *
     * @return bool
     */
    public function isJavascript();

    /**
     * Returns true if Content-Type header indicates that
     * it has Html content.
     *
     * @return boolean
     */
    public function isHtml();

    /**
     * Returns true if Content-Type header indicates that
     * it has Xml content.
     *
     * @return boolean
     */
    public function isUrlEncoded();

}