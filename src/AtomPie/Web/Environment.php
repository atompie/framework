<?php
namespace AtomPie\Web {

    use Generi\Object;
    use AtomPie\Web\Boundary\IAmEnvironment;
    use AtomPie\Web\Boundary\IAmSession;
    use AtomPie\System\Environment\EnvVariable;
    use AtomPie\Web\Connection\Http\Content;
    use AtomPie\Web\Connection\Http\Header;
    use AtomPie\Web\Connection\Http\Header\ContentType;
    use AtomPie\Web\Connection\Http\Request;
    use AtomPie\Web\Connection\Http\Response;

    class Environment extends Object implements IAmEnvironment
    {

        /**
         * @var Environment
         */
        private static $oInstance;

        /**
         * @var Request
         */
        private $oRequest;

        /**
         * @var Response
         */
        private $oResponse;

        /**
         * @var \AtomPie\System\Environment\EnvVariable
         */
        private $oEnv;

        /**
         * @var IAmSession
         */
        private $oSession;

        /**
         * @var Server
         */
        private $oServer;

        static private function factoryRequest()
        {

            $oRequest = new Request();
            $oRequest->load();

            return $oRequest;
        }

        static private function factoryResponse()
        {

            $oResponse = new Response();
            // TODO set content-type based on Client accept header.
            // TODO sprawdzane jest to gdzieś pózniej a powinno być tutaj.
            // TODO bo może wystąpić błąd w Middleware i wtedy nie wiemy
            // TODO w jakim formacie go zwrócić.
            $oResponse->setContent(new Content('', new ContentType(ContentType::HTML)));

            return $oResponse;
        }

        private function __construct(
            Request $oRequest,
            Response $oResponse,
            IAmSession $oSession,
            EnvVariable $oEnv,
            Server $oServer
        ) {

            // Auto set Response Content-Type depending on Accept header of Request.
            $oResponse = $this->autoSetResponseContentTypeFromAcceptHeader($oRequest, $oResponse);

            $this->oResponse = $oResponse;
            $this->oRequest = $oRequest;
            $this->oSession = $oSession;
            $this->oEnv = $oEnv;
            $this->oServer = $oServer;

        }

        /**
         * @param Request $oRequest
         * @param $aAvailableResponseContentTypesByPriority
         * @return null
         */
        private function getAcceptableContentType($oRequest, $aAvailableResponseContentTypesByPriority)
        {

            /** @var Header\Accept $oAccept */
            $oAccept = $oRequest->getHeader(Header::ACCEPT);
            if (false !== $oAccept) {
                $aMimeTypes = $oAccept->getMediaTypesByPriority();
                foreach ($aMimeTypes as $oMimeType) {
                    foreach ($aAvailableResponseContentTypesByPriority as $sContentType) {
                        if ($oMimeType->willYouAccept($sContentType)) {
                            return $oMimeType->getMedia();
                        }
                    }
                }
            }
            return null;
        }

//        static public function getInstance1(IAmSession $oSessionHandler)
//        {
//            if (!isset(self::$oInstance)) {
//
//                ini_set('request_order', 'GP');
//                $oRequest = self::factoryRequest();
//                $oResponse = self::factoryResponse();
//                $oEnvVariables = EnvVariable::getInstance();
//                $oServer = Server::getInstance();
//
//                self::$oInstance = new self($oRequest, $oResponse, $oSessionHandler, $oEnvVariables, $oServer);
//            }
//
//            return self::$oInstance;
//        }

        /**
         * @return Environment
         */
        static public function getInstance()
        {


            if (!isset(self::$oInstance)) {

                ini_set('request_order', 'GP');
                $oRequest = self::factoryRequest();
                $oResponse = self::factoryResponse();
                $oSession = Session::getInstance();
                $oEnvVariables = EnvVariable::getInstance();
                $oServer = Server::getInstance();

                self::$oInstance = new self($oRequest, $oResponse, $oSession, $oEnvVariables, $oServer);
            }

            return self::$oInstance;
        }

        static public function destroyInstance()
        {
            EnvVariable::destroyInstance();
            Session::destroyInstance();
            self::$oInstance = null;
        }

        /**
         * @return Environment
         */
        static public function resetInstance()
        {
            self::destroyInstance();
            return self::getInstance();
        }

        /**
         * @return Request
         */
        public function getRequest()
        {
            return $this->oRequest;
        }

        /**
         * @return Response
         */
        public function getResponse()
        {
            return $this->oResponse;
        }

        /**
         * @return \AtomPie\System\Environment\EnvVariable
         */
        public function getEnv()
        {
            return $this->oEnv;
        }

        /**
         * @return IAmSession
         */
        public function getSession()
        {
            return $this->oSession;
        }

        /**
         * @return Server
         */
        public function getServer()
        {
            return $this->oServer;
        }

        /**
         * @param Request $oRequest
         * @param Response $oResponse
         * @return Response
         */
        private function autoSetResponseContentTypeFromAcceptHeader($oRequest, $oResponse)
        {
            $aISendContentTypesByPriority = array(
                Header\ContentType::HTML,
                Header\ContentType::HTML . ';charset=utf-8',
                Header\ContentType::JSON,
                Header\ContentType::JSON . ';charset=utf-8',
                Header\ContentType::XML,
                Header\ContentType::XML . ';charset=utf-8'
            );

            $sContentType = $this->getAcceptableContentType($oRequest, $aISendContentTypesByPriority);
            if ($sContentType !== null && $sContentType !== '*/*') {
                // TODO code smell should have one place with content-type.
                $oResponse->setContentType($sContentType);
                $oResponse->addHeader(Header::CONTENT_TYPE, $sContentType);
            }

            return $oResponse;
        }
    }
}
