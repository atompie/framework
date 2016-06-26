<?php
namespace AtomPie\Web\Connection\Http\Header {

    use AtomPie\Web\Boundary\IAmContentType;
    use AtomPie\Web\Connection\Http\Header;

    /**
     * Content-Type header.
     */
    class ContentType extends Header implements IAmContentType
    {

        const PDF = 'application/pdf';
        const JSON = 'application/json';
        const JAVASCRIPT = 'application/javascript';
        const URL_ENCODED = 'application/x-www-form-urlencoded';
        const BINARY = 'application/octet-stream';
        const XML = 'application/xml';
        const HTML = 'text/html';
        const PLAINTEXT = 'text/plain';
        const PNG = 'image/png';
        const JPEG = 'image/jpeg';
        const GIF = 'image/git';

        /**
         * @var MediaType
         */
        private $oMediaType;

        /**
         * Holds information on content-type header.
         *
         * @param string $sContentType
         */
        public function __construct($sContentType)
        {
            parent::__construct(Header::CONTENT_TYPE, $sContentType);
            $this->oMediaType = MediaType::parseMediaType($sContentType);
        }

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
        public function isJson()
        {
            return false !== strstr($this->getMediaType(), self::JSON) || false !== strstr($this->oMediaType->type,
                'text/json');
        }

        /**
         * Returns true if Content-Type header indicates that
         * it has Xml content.
         *
         * @return boolean
         */
        public function isXml()
        {
            return false !== strstr($this->getMediaType(), self::XML);
        }

        /**
         * Returns true if Content-Type header indicates that
         * it has Text content.
         *
         * @return boolean
         */
        public function isText()
        {
            return false !== strstr($this->getMediaType(), self::PLAINTEXT);
        }

        /**
         * Returns true if Content-Type header indicates that
         * it has Javascript content.
         *
         * @return bool
         */
        public function isJavascript()
        {
            return false !== strstr($this->getMediaType(), self::JAVASCRIPT);
        }

        /**
         * Returns true if Content-Type header indicates that
         * it has Html content.
         *
         * @return boolean
         */
        public function isHtml()
        {
            return false !== strstr($this->getMediaType(), self::HTML);
        }

        /**
         * Returns true if Content-Type header indicates that
         * it has Xml content.
         *
         * @return boolean
         */
        public function isUrlEncoded()
        {
            return false !== strstr($this->getMediaType(), self::URL_ENCODED);
        }

        /**
         * @return string
         */
        public function getMediaType()
        {
            return $this->oMediaType->getMedia();
        }

        /**
         * Returns media param.
         *
         * @param $sParamName
         * @return null| string
         */
        public function getParam($sParamName)
        {
            return (isset($this->oMediaType->params[$sParamName]))
                ? $this->oMediaType->params[$sParamName]
                : null;
        }
    }
}