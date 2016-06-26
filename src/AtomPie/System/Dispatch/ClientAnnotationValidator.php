<?php
namespace AtomPie\System\Dispatch {

    use AtomPie\Core\Annotation\Tag\Client;
    use AtomPie\I18n\Label;
    use AtomPie\Web\Boundary\IAmRequest;
    use AtomPie\Web\Boundary\IHaveHeaders;
    use AtomPie\Web\Boundary\IHaveHttpMethod;
    use \AtomPie\Web\Connection\Http;
    use AtomPie\Web\Connection\Http\Header\Status;
    use AtomPie\Web\Connection\Http\Request;

    class ClientAnnotationValidator
    {

        /**
         * @var Client
         */
        private $oClientTag;

        public function __construct(Client $oClientTag)
        {

            $this->oClientTag = $oClientTag;
        }

        /**
         * @param IAmRequest $oRequest
         * @throws DispatchException
         */
        public function validate(IAmRequest $oRequest)
        {
            $this->validateAccept($oRequest);
            $this->validateMethod($oRequest);
            $this->validateContentType($oRequest);
            $this->validateType($oRequest);
        }

        /**
         * @param IAmRequest $oRequest
         * @return bool
         * @throws DispatchException
         */
        public function validateContentType($oRequest)
        {

            if (!empty($this->oClientTag->ContentType)) {

                if ($oRequest->getMethod() == Request\Method::GET) {
                    throw new DispatchException(
                        new Label(
                            'Can not check content-type on request send with GET method.'
                        )
                    );
                }

                if (!$oRequest->hasHeader(Http\Header::CONTENT_TYPE)) {
                    throw new DispatchException(
                        sprintf(
                            new Label(
                                'Could not read content-type header from request. Available headers [%s]'
                            ),
                            implode(',', array_keys($oRequest->getHeaders()))
                        ),
                        Status::UNSUPPORTED_MEDIA_TYPE
                    );
                }

                $sContentType = $oRequest->getHeader(Http\Header::CONTENT_TYPE)->getValue();

                if (0 != strcasecmp($sContentType, $this->oClientTag->ContentType)) {
                    throw new DispatchException(
                        sprintf(
                            new Label('Incorrect request content-type. Expected [%s], given [%s]'),
                            $this->oClientTag->ContentType,
                            $sContentType
                        ),
                        Http\Header\Status::UNSUPPORTED_MEDIA_TYPE
                    );
                }

            }
            return true;
        }

        /**
         * @param IAmRequest $oRequest
         * @return bool
         * @throws DispatchException
         */
        public function validateType($oRequest)
        {
            if (!empty($this->oClientTag->Type)) {
                $bIsCorrect = true;

                if (strtolower($this->oClientTag->Type) == 'ajax') {
                    $bIsCorrect = $oRequest->isAjax();
                } else {
                    if (strtolower($this->oClientTag->Type) == 'cli') {
                        $bIsCorrect = $this->isCli();
                    } else {
                        if (strtolower($this->oClientTag->Type) == 'webrequest') {
                            $bIsCorrect = !$this->isCli();
                        }
                    }
                }

                if (!$bIsCorrect) {

                    throw new DispatchException(
                        sprintf(
                            new Label('Incorrect client type. Expected [%s].'),
                            $this->oClientTag->Type
                        ),
                        Http\Header\Status::FORBIDDEN
                    );

                }
            }
            return true;
        }

        /**
         * @param IHaveHttpMethod $oRequest
         * @return bool
         * @throws DispatchException
         */
        public function validateMethod($oRequest)
        {
            if (!empty($this->oClientTag->Method)) {
                $aMethods = explode(',', strtoupper($this->oClientTag->Method));
                $sRequestMethod = $oRequest->getMethod();
                if (!in_array(strtoupper($sRequestMethod), $aMethods)) {
                    throw new DispatchException(
                        sprintf(
                            new Label('Incorrect method. Expected [%s], given [%s].'),
                            $this->oClientTag->Method,
                            $sRequestMethod
                        ),
                        Status::METHOD_NOT_ALLOWED
                    );
                }
            }
            return true;
        }

        /**
         * @param IHaveHeaders $oRequest
         * @return bool
         * @throws DispatchException
         */
        public function validateAccept($oRequest)
        {
            if ($oRequest->hasHeader(Http\Header::ACCEPT)) {

                /** @var Http\Header\Accept $oAcceptHeader */
                $oAcceptHeader = $oRequest->getHeader(Http\Header::ACCEPT);

                if (isset($this->oClientTag->Accept)) {

                    if (!$oAcceptHeader->willYouAcceptMediaType($this->oClientTag->Accept, false)) {

                        throw new DispatchException(
                            sprintf(
                                new Label('EndPoint requires that client accepts %s media-type. Client accepts %s.'),
                                $this->oClientTag->Accept,
                                $oAcceptHeader->getValue()
                            ),
                            Status::NOT_ACCEPTABLE
                        );

                    }

                }
            }

            return true;
        }

        /**
         * @return bool
         */
        private function isCli()
        {
            return (php_sapi_name() === 'cli' || defined('STDIN'));
        }
    }

}
