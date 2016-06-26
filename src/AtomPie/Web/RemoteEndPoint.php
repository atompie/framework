<?php
namespace AtomPie\Web {

    use AtomPie\Core\Dispatch\QueryString;
    use AtomPie\Web\Connection\Http\Content;
    use AtomPie\Web\Connection\Http\Header\ContentType;
    use AtomPie\Web\Connection\Http\Header\Status;
    use AtomPie\Web\Connection\Http\Request;
    use AtomPie\Web\Connection\Http\Response;
    use AtomPie\Web\Connection\Http\Url\Param;

    class RemoteEndPoint extends QueryString
    {

        const DEFAULT_SEPARATOR = '.';
        private $sClassName;
        private $sUri;

        public function __construct($sUri, $sClassName)
        {
            $this->sClassName = $this->urlEscape($sClassName);
            $this->sUri = $sUri;
        }

        public function call($sMethod, array $aArgs, \Closure $pOnSuccess = null)
        {

            $sQuery = $this->sClassName . self::DEFAULT_SEPARATOR . $sMethod;
            $oRequest = new Request(Request::POST);

            $aParameters = array();
            foreach ($aArgs as $oArg) {
                if ($oArg instanceof Param) {
                    $aParameters[$oArg->getName()] = $oArg->getValue();
                }
            }

            // Only json content type is available
            $oRequest->setContent(new Content(json_encode($aParameters), new ContentType(ContentType::JSON)));

            $sRequestUrl = $this->sUri . '/' . $sQuery;
            $oResponse = $oRequest->send($sRequestUrl);

            if ($oResponse->getStatus()->is(Status::NOT_FOUND)) {
                throw new Exception(sprintf('Page %s not found.', $sRequestUrl));
            } elseif ($oResponse->getStatus()->is(Status::INTERNAL_SERVER_ERROR)) {
                $this->throwException($oResponse);

                return null;
            } else {
                $mOutput = $this->getContent($oResponse);

                if ($pOnSuccess === null) {
                    return $mOutput;
                }

                return $pOnSuccess($mOutput);
            }
        }

        private function throwException(Response $oResponse)
        {
            if ($oResponse->getContent()->getContentType()->isJson()) {
                $oOutput = $oResponse->getContent()->decodeAsJson();
                $sMessage = sprintf('Remote Exception thrown with mesage [%s] in line %s within file %s',
                    $oOutput->ErrorMessage, $oOutput->Line, $oOutput->File);
                $oException = new Exception($sMessage);
                throw $oException;
            } elseif ($oResponse->getContent()->getContentType()->isXml()) {
                /** @var \SimpleXMLElement $oOutput */
                $oOutput = $oResponse->getContent()->getAsSimpleXml();
                /** @noinspection PhpUndefinedFieldInspection */
                $oException = new Exception((string)$oOutput->ErrorMessage);
                throw $oException;
            } else {
                throw new Exception('Exception was thrown and transferred to client using unsupported content-type.');
            }
        }

        /**
         * @param $oResponse
         * @return mixed
         */
        private function getContent(Response $oResponse)
        {
            if ($oResponse->getContent()->getContentType()->isJson()) {
                $aOutput = $oResponse->getContent()->decodeAsJson();
                return $aOutput;
            } elseif ($oResponse->getContent()->getContentType()->isXml()) {
                $aOutput = $oResponse->getContent()->getAsSimpleXml();
                return $aOutput;
            } else {
                $aOutput = $oResponse->getContent()->get();
                return $aOutput;
            }
        }

    }

}
