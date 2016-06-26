<?php
namespace AtomPie\Web\Remote {

    use AtomPie\Web\Connection\Http;
    use AtomPie\Web\Cookie;
    use AtomPie\Web\Exception;
    use AtomPie\Web\SSL\NoPeerVerification;

    class RemoteService
    {
        /**
         * @var string
         */
        protected $sHost;

        /**
         * @var \AtomPie\Web\Connection\Http\Request
         */
        private $oRequest;

        /**
         * try{
         *    $oAcl = new Acl($sHost);
         *    if($oAcl->openProject($sHash)) {
         *       $oJsonData = $oAcl->loginUser($sLogin, $sPassword);
         *       // Process Json Data
         * }
         *    $oAcl->closeProject();
         * } catch(\Exception $e) {
         *    echo $e->getMessage();
         * }
         * @param $sHost
         */
        public function __construct($sHost)
        {
            $this->sHost = $sHost;
            $this->oRequest = new Http\Request(Http\Request\Method::POST);
            $this->oRequest->setSSLPeerVerification(new NoPeerVerification());
        }

        /**
         * @param $sUri
         * @param $sHash
         * @throws Exception
         * @return bool
         */
        protected function open($sUri, $sHash)
        {
            $oResponseData = $this->send(
                $sUri,
                array('Hash' => $sHash)
            );

            $bResult = $oResponseData->getContent(Http\Header\ContentType::JSON);

            $this->setSessionId($oResponseData->getResponse());

            return $bResult;
        }

        /**
         * @param $sUri
         * @return ResponseData
         */
        protected function close($sUri)
        {
            return $this->send($sUri, array());
        }

        /**
         * @param $sContent
         * @param \AtomPie\Web\Connection\Http\Header\ContentType $oContentType
         */
        protected function setContent($sContent, Http\Header\ContentType $oContentType = null)
        {
            $this->oRequest->setContent(new Http\Content($sContent, $oContentType));
        }

        /**
         * @param \AtomPie\Web\Connection\Http\Response $oResponse
         */
        private function setSessionId(Http\Response $oResponse)
        {
            $oCookie = $oResponse->getCookies()->get(session_name());
            /** @var Cookie $oCookie */
            $this->oRequest->addCookie($oCookie);
        }

        /**
         * @param $sUri
         * @param null $aParams
         * @return ResponseData
         * @throws Exception
         */
        protected function send($sUri, $aParams = null)
        {

            $oResponse = $this->oRequest->send(
                $this->sHost . $sUri,
                $aParams
            );

            return new ResponseData($oResponse);

        }
    }
}