<?php
namespace AtomPie\Web\Connection\Http {

    use AtomPie\System\IO\File;
    use AtomPie\Web\Boundary\IAmContent;
    use AtomPie\Web\Boundary\IAmContentType;
    use AtomPie\Web\Connection\Http\Header\ContentType;

    /**
     * Holds information on content. References \Web\Connection\Http\ContentType to indicate
     * possible content type.
     */
    class Content implements IAmContent
    {
        /**
         * Hold content. Can be string or any object with  __toString method implemented.
         * Constructor will __toString if not string.
         *
         * @var string
         */
        private $sContent;
        /**
         * @var ContentType
         */
        private $oContentType;
        /**
         * Content length.
         *
         * @var int
         */
        private $iContentLength;

        /**
         *
         * @param string $sContent Content, mostly string or Template
         * @param ContentType $oContentType
         */
        public function __construct($sContent, ContentType $oContentType = null)
        {
            if (is_null($oContentType)) {
                $oContentType = new ContentType('text/html; charset=utf-8');
            }
            $this->set($sContent, $oContentType);
        }

        /**
         * @return bool
         */
        public function isFile()
        {
            return $this->sContent instanceof File;
        }

        /**
         * Returns content as a string.
         *
         * @return string
         */
        public function __toString()
        {
            if ($this->sContent instanceof File) {
                return $this->sContent->loadRaw();
            } else {
                if (null === $this->sContent) {
                    return '';
                }
            }
            return (string)$this->get();
        }

        /**
         * Content length.
         *
         * @return int $iContentLength
         */
        public final function getContentLength()
        {
            return $this->iContentLength;
        }

        /**
         * Content type.
         *
         * @return ContentType
         */
        public function getContentType()
        {
            return $this->oContentType;
        }

        /**
         * @return bool
         */
        public function hasContentType()
        {
            return isset($this->oContentType);
        }

        /**
         * Return content
         *
         * @return string
         */
        public function get()
        {
            return $this->sContent;
        }

        /**
         * Returns true if content is not empty and false if oposit.
         *
         * @return boolean
         */
        public function notEmpty()
        {
            return !empty($this->sContent);
        }

        /**
         * Sets content.
         *
         * @param string $sContent
         * @param IAmContentType $oContentType
         */
        public function set($sContent, IAmContentType $oContentType = null)
        {
            $this->setContent($sContent);
            $this->setContentType($oContentType);
        }

        /**
         * @param $sContent
         */
        public function setContent($sContent)
        {
            if (null !== $sContent) {
                if ($sContent instanceof File) {
                    $this->sContent = $sContent;
                    $this->iContentLength = $sContent->getSize();
                } else {
                    if (is_string($sContent)) {
                        $this->sContent = $sContent;
                    }
                    if (is_array($sContent)) {
                        $this->sContent = http_build_query($sContent);
                    } else {
                        $this->sContent = (string)$sContent;
                    }
                    $this->iContentLength = strlen($this->sContent);
                }
            } else {
                $this->sContent = '';
                $this->iContentLength = 0;
            }
        }

        /**
         * @param IAmContentType $oContentType
         */
        public function setContentType(IAmContentType $oContentType)
        {
            if (!is_null($oContentType)) {
                $this->oContentType = $oContentType;
            }
        }

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
        public function decodeAsJson($bAssoc = null)
        {
            return json_decode($this->get(), $bAssoc);
        }

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
        public function encodeAsJson($iOptions = null)
        {
            return json_encode($this->get(), $iOptions);
        }

        /**
         * @return \SimpleXMLElement
         */
        public function getAsSimpleXml()
        {
            return @new \SimpleXMLElement($this->get());
        }

    }
}