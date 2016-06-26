<?php
namespace AtomPie\Service\Api {

    use AtomPie\I18n\Label;
    use Generi\Exception;
    use AtomPie\Web\Connection\Http;
    use AtomPie\Web\Connection\Http\Header\Status;

    class ResponseData
    {

        /**
         * @var Http\Response
         */
        private $oResponse;
        /**
         * @var mixed
         */
        private $mContent;
        /**
         * @var \SimpleXMLElement
         */
        public $XmlData;

        /**
         * @param Http\Response $oResponse
         * @throws Exception
         */
        public function __construct(Http\Response $oResponse)
        {
            $this->oResponse = $oResponse;

            if ($this->isJson()) {
                $oJsonData = $this->oResponse->getContent()->decodeAsJson();

                if (is_null($oJsonData)) {
                    throw new Exception('Could not decode json string.');
                }

                if ($this->oResponse->getStatus()->is(Status::INTERNAL_SERVER_ERROR)) {
                    if (isset($oJsonData->Message)) {
                        throw new Exception($oJsonData->Message);
                    }
                }

                $this->mContent = $oJsonData;

            } else {
                if ($this->isXml()) {
                    $this->mContent = $this->oResponse->getContent()->getAsSimpleXml();
                } else {
                    $this->mContent = $this->oResponse->getContent()->get();
                }
            }
        }

        /**
         * @return Http\Response
         */
        public function getResponse()
        {
            return $this->oResponse;
        }

        /**
         * @return Http\Header\ContentType
         */
        public function getContentType()
        {
            return $this->oResponse->getContent()->getContentType();
        }

        /**
         * @param $sExpectedContentType
         * @return mixed|string
         * @throws Exception
         */
        public function getContent($sExpectedContentType)
        {
            switch ($sExpectedContentType) {
                case Http\Header\ContentType::JSON:
                    if (!$this->isJson()) {
                        throw new Exception(new Label('Expected JSON content in response.'));
                    }
                    break;
                case Http\Header\ContentType::HTML:
                    if (!$this->isHtml()) {
                        throw new Exception(new Label('Expected HTML content in response.'));
                    }
                    break;
                case Http\Header\ContentType::XML:
                    if (!$this->isXml()) {
                        throw new Exception(new Label('Expected XML content in response.'));
                    }
                    break;
                default:
                    if ($this->getContentType()->getValue() != $sExpectedContentType) {
                        throw new Exception(sprintf(new Label('Expected %s content in response.',
                            $sExpectedContentType)));
                    }
                    break;
            }

            return $this->mContent;
        }

        /**
         * @return bool
         */
        public function isJson()
        {
            return $this->oResponse->getContent()->getContentType()->isJson();
        }

        /**
         * @return bool
         */
        public function isXml()
        {
            return $this->oResponse->getContent()->getContentType()->isXml();
        }

        /**
         * @return bool
         */
        public function isHtml()
        {
            return $this->oResponse->getContent()->getContentType()->isHtml();
        }

    }
}