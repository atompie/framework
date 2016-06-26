<?php
namespace AtomPie\Web\Connection\Http {

    use Generi\Boundary\ICollection;
    use Generi\Boundary\IStringable;
    use Generi\Collection;
    use AtomPie\I18n\Label;
    use AtomPie\Web\Boundary\IAmHttpHeader;
    use AtomPie\Web\Boundary\IAmRequest;
    use AtomPie\Web\Connection\Http\Header\ContentType;
    use AtomPie\Web\Connection\Http\Header\Status;
    use AtomPie\Web\Connection\ProxyCredentials;
    use AtomPie\Web\Cookie;
    use AtomPie\Web\CookieJar;
    use AtomPie\Web\Exception;
    use AtomPie\Web\SSL\PeerVerification;

    class ImmutableRequest implements IAmRequest
    {

        const USER_AGENT_GOOGLE_BOT = 'Googlebot/2.1 (+http://www.googlebot.com/bot.html)';
        const USER_AGENT_YAHOO = 'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)';
        const USER_AGENT_FIREFOX = 'Mozilla/5.0 (X11; U; Linux i686; pl-PL; rv:1.7.10) Gecko/20050717 Firefox/1.0.6';
        const USER_AGENT_OPERA = 'Mozilla/4.0 (compatible; MSIE 6.0; X11; Linux i686; en) Opera 8.01';
        const USER_AGENT_IE = 'Mozilla/4.0 (compatible; MSIE 9.0; Windows 8)';
        const USER_AGENT_DEFAULT = 'PhpWorkshop/1.0 (Linux i686)';

        const GET = 'get';
        const PUT = 'put';
        const POST = 'post';
        const DELETE = 'delete';
        /**
         * @var string
         */
        private $sRequestString;
        /**
         * @var Content
         */
        protected $oContent;
        /**
         * @var array
         */
        protected $aHeaders = array();
        protected $iHeadersSize;
        protected $iTimeOut = 10;


        /**
         * @var PeerVerification
         */
        protected $oSslPeerVerification;
        /**
         * @var string
         */
        protected $sProxy;
        /**
         * @var Url
         */
        protected $oHttpUrl;
        /**
         * @var string
         */
        protected $sMethod;
        /**
         * Holds request parameters.
         *
         * @var \Generi\Boundary\ICollection
         */
        protected $oRequestCollection;
        /**
         * @var \Generi\Boundary\ICollection
         */
        protected $oPostRequestCollection;
        /**
         * @var ICollection
         */
        protected $oGetRequestCollection;
        /**
         * @var string
         */
        private $sRemoteAddr;
        /**
         * @var ProxyCredentials
         */
        protected $oCredentials;
        /**
         * @var string
         */
        protected $sReferrerUrl;

        /**
         *
         * @param string $sMethod use    \Web\Connection\Http\Request::GET,
         *                               \Web\Connection\Http\Request::POST,
         *                               \Web\Connection\Http\Request::PUT,
         *                               \Web\Connection\Http\Request::DELETE
         */
        public function __construct($sMethod = null)
        {

            $this->oGetRequestCollection = new Collection();
            $this->oPostRequestCollection = new Collection();
            $this->oRequestCollection = new Collection();

            if (is_null($sMethod)) {
                $this->sMethod = self::GET;
            } else {
                $this->sMethod = $sMethod;
            }
        }

        /**
         * Returns method.
         *
         * @return string
         */
        public function getMethod()
        {
            return $this->sMethod;
        }

        /**
         * Returns TRUE if method is set, FLASE if oposit.
         *
         * @param string $sMethod use
         *                    \Web\Connection\Http\Request::GET,
         *                    \Web\Connection\Http\Request::POST,
         *                    \Web\Connection\Http\Request::PUT,
         *                    \Web\Connection\Http\Request::DELETE
         *                    instead of string
         *
         * @return boolean
         */
        public function isMethod($sMethod)
        {
            return strtolower($this->sMethod) == strtolower($sMethod);
        }

        /**
         * Returns TRUE if request has set URL, FALSE if oposit.
         *
         * @return bool
         */
        public function hasUrl()
        {
            return isset($this->oHttpUrl) && $this->oHttpUrl instanceof Url;
        }

        /**
         * Returns \Web\Connection\Http\Url.
         *
         * @return Url $oHttpUrl
         */
        public function getUrl()
        {
            return $this->oHttpUrl;
        }

        /**
         * Returns time-out.
         *
         * @return int
         */
        public function getTimeOut()
        {
            return $this->iTimeOut;
        }

        /**
         * @param null $sMethod
         * @return \Generi\Boundary\ICollection | null
         */
        public function getAllParams($sMethod = null)
        {

            if (is_null($sMethod)) {
                return $this->oRequestCollection;
            }

            if ($sMethod == Request::GET) {
                return $this->oGetRequestCollection;
            }

            if ($sMethod == Request::POST) {
                return $this->oPostRequestCollection;
            }

            return null;

        }

        /**
         * Returns request parameter.
         *
         * @param string $sName
         * @param null $sMethod
         * @throws Exception
         * @return string | null
         */
        public function getParam($sName, $sMethod = null)
        {

            if ($sName instanceof IStringable) {
                $sName = $sName->__toString();
            }

            if (!isset($this->oRequestCollection)) {
                throw new Exception('Please load() parameters to request object.');
            }

            if ($sMethod == Request::GET) {
                return ($this->oGetRequestCollection->has($sName))
                    ? $this->oGetRequestCollection->get($sName)
                    : null;
            }

            if ($sMethod == Request::POST) {
                return ($this->oPostRequestCollection->has($sName))
                    ? $this->oPostRequestCollection->get($sName)
                    : null;
            }

            if ($this->oRequestCollection->has($sName)) {
                return $this->oRequestCollection->get($sName);
            }

            return null;
        }

        /**
         * @param string $sName
         * @param string $sMethod
         * @throws Exception
         * @return bool
         */
        public function hasParam($sName, $sMethod = null)
        {
            if (!isset($this->oRequestCollection)) {
                throw new Exception('Please load() parameters to request object.');
            }

            if ($sMethod == Request::GET) {
                return $this->oGetRequestCollection->has($sName);
            }

            if ($sMethod == Request::POST) {
                return $this->oPostRequestCollection->has($sName);
            }

            return $this->oRequestCollection->has($sName);
        }

        /**
         * @return bool
         */
        public function isAjax()
        {
            return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        }

        /**
         * Loads data from received request.
         *
         * @return Request
         */
        public function load()
        {

            $this->parseRequestParams();

            foreach ($_SERVER as $sName => $sValue) {
                if (substr($sName, 0, 5) == "HTTP_") {
                    $sName = str_replace(" ", "-", ucwords(strtolower(str_replace("_", " ", substr($sName, 5)))));
                    $this->__addHeader($sName, $sValue);
                }
            }

            if (isset($_SERVER['REQUEST_METHOD'])) {
                $this->__setMethod(strtolower($_SERVER['REQUEST_METHOD']));
            }

            // Adds multiple cookies
            $this->loadCookies();

            if (isset($_SERVER['REMOTE_ADDR'])) {
                $this->sRemoteAddr = $_SERVER['REMOTE_ADDR'];
            }

            if (isset($_SERVER['QUERY_STRING'])) {
                $this->__setRequestString($_SERVER['QUERY_STRING']);
            }

            if ($this->getMethod() != self::GET) {
                // TODO repair
                if (!isset($_SERVER["CONTENT_TYPE"])) {
                    $_SERVER["CONTENT_TYPE"] = ContentType::HTML;
                }

                $sContent = $this->getRawData();
                $oContentEncodingHeader = $this->getHeader('Content-Encoding');

                if ($oContentEncodingHeader != false && strstr($oContentEncodingHeader->getValue(), 'gzip') == 'gzip') {
                    $sContent = gzuncompress($sContent);
                }

                $this->__setContent(new Content($sContent, new ContentType($_SERVER["CONTENT_TYPE"])));
            }

            return $this;
        }


        /**
         * Sets content.
         *
         * @param Content $oContent
         */
        protected function __setContent(Content $oContent)
        {
            $this->oContent = $oContent;
            if ($oContent->hasContentType()) {
                $this->__addHeader(Header::CONTENT_TYPE, $oContent->getContentType());
            }
        }

        /**
         * Adds header to request.
         *
         * @param string $sName
         * @param string | IAmHttpHeader $sValue
         */
        protected function __addHeader($sName, $sValue)
        {
            $sName = strtoupper($sName);
            if ($sValue instanceof Header) {
                $this->aHeaders[$sName] = $sValue;
            } else {
                if ($sName == Header::ACCEPT) {
                    $this->aHeaders[$sName] = new Header\Accept($sValue);
                } else {
                    if ($sName == Header::AUTHORIZATION) {
                        $this->aHeaders[$sName] = new Header\Authorization($sValue);
                    } else {
                        if ($sName == Header::CONTENT_TYPE) {
                            $this->aHeaders[$sName] = new Header\ContentType($sValue);
                        } else {
                            $this->aHeaders[$sName] = new Header($sName, $sValue);
                        }
                    }
                }
            }
        }

        /**
         * Sets method. use \Web\Connection\Http\Request::GET,
         *                    \Web\Connection\Http\Request::POST,
         *                    \Web\Connection\Http\Request::PUT,
         *                    \Web\Connection\Http\Request::DELETE
         *
         * @param string $sMethod
         */
        protected function __setMethod($sMethod)
        {
            $this->sMethod = $sMethod;
        }

        /**
         * @param string $sRequestString
         */
        protected function __setRequestString($sRequestString)
        {
            $this->sRequestString = $sRequestString;
        }


        protected function loadCookies()
        {
            if (isset($_COOKIE)) {

                $oCookieJar = $this->getCookies();

                foreach ($_COOKIE as $sKey => $sValue) {
                    $oCookieJar->add(new Cookie($sKey, $sValue));
                }

            }
        }

        /**
         * @return string
         */
        public function getRemoteAddress()
        {
            return $this->sRemoteAddr;
        }

        /**
         * Returns TRUE if content is JSON encoded, FALSE if oposit and throws
         * Exception if content not available.
         *
         * @throws \Exception
         * @return boolean
         */
        public function isContentAsJson()
        {
            if ($this->getMethod() == self::GET) {
                throw new Exception('Requests send via GET method do not have content!');
            }
            if (!$this->hasContent()) {
                throw new Exception('Request has not content!');
            }
            return $this->getContent()->getContentType()->isJson() && !is_null($this->decodeContentAsJson());
        }

        /**
         * Decodes JSON content. Throws Exception if content not available.
         *
         * @throws \Exception
         * @return mixed
         */
        public function decodeContentAsJson()
        {
            if ($this->hasContent()) {
                return $this->getContent()->decodeAsJson();
            }

            throw new Exception('Request has not content!');
        }

        private function parseRequestParams()
        {

            unset($_REQUEST[session_name()]);
            unset($_REQUEST['statID']);
            unset($_GET[session_name()]);
            unset($_GET['statID']);
            unset($_POST[session_name()]);
            unset($_POST['statID']);

            $aRequest = $_REQUEST;
            $aPost = $_POST;
            $aGet = $_GET;

            if (empty($aRequest)) {
                if (!empty($aPost)) {
                    $this->oRequestCollection = new Collection($aPost);
                } else {
                    if (!empty($aGet)) {
                        $this->oRequestCollection = new Collection($aGet);
                    }
                }
            } else {
                $this->oRequestCollection = new Collection($aRequest);
            }
            $this->oPostRequestCollection = new Collection($aPost);
            $this->oGetRequestCollection = new Collection($aGet);
        }

        /**
         * @return string
         */
        public function getReferrerUrl()
        {
            return $this->sReferrerUrl;
        }

        /**
         * @return bool
         */
        public function hasReferrerUrl()
        {
            return isset($this->sReferrerUrl);
        }

        /**
         * @return bool
         */
        public function hasProxy()
        {
            return isset($this->sProxy);
        }

        /**
         * @return bool
         */
        public function hasProxyCredentials()
        {
            return isset($this->oCredentials);
        }

        /**
         * @return ProxyCredentials
         */
        public function getProxyCredentials()
        {
            return $this->oCredentials;
        }

        /**
         * @return string
         */
        public function getProxy()
        {
            return $this->sProxy;
        }

        /**
         * @param $sParamName
         * @return bool
         */
        public function hasUploadedFile($sParamName)
        {
            return isset($_FILES[$sParamName]);
        }

        /**
         * @param $sParamName
         * @return UploadFile
         */
        public function getFile($sParamName)
        {
            return new UploadFile($sParamName);
        }

        public function __toString()
        {
            $sHeader = implode("\n", $this->getHeaders());
            $sHeader .= "\n\n";
            $sHeader .= $this->getContent();

            return $sHeader;
        }

        /**
         * @return string $sRequestString
         */
        public function getRequestString()
        {
            return $this->sRequestString;
        }

        /**
         * @return CookieJar
         */
        public function getCookies()
        {
            return CookieJar::getInstance();
        }

        /**
         * @return bool
         */
        public function hasCookies()
        {
            return !CookieJar::getInstance()->isEmpty();
        }

        public function getHeadersSize()
        {
            return $this->iHeadersSize;
        }

        /**
         * Returns TRUE if has header $sName.
         *
         * @param string $sName
         * @return bool
         */
        public final function hasHeader($sName)
        {
            return isset($this->aHeaders[strtoupper($sName)]);
        }

        /**
         * Returns array of headers (Web\Connection\Http\Header class).
         *
         * @return Header[]
         */
        public final function getHeaders()
        {
            return $this->aHeaders;
        }

        /**
         * Returns TRUE if header collection is not empty.
         *
         * @return bool
         */
        public final function hasHeaders()
        {
            return !empty($this->aHeaders);
        }

        /**
         * Returns TRUE if HTTP communication has content.
         *
         * @return bool
         */
        public function hasContent()
        {
            return isset($this->oContent) && $this->oContent->notEmpty();
        }

        private function getRawData()
        {
            return file_get_contents('php://input');
        }

        /**
         * Returns $sName header.
         * Remember to populate header values with load method.
         *
         * @param string $sName
         * @return Header | boolean
         */
        public final function getHeader($sName)
        {
            $sName = strtoupper($sName);
            if ($this->hasHeader($sName)) {
                return $this->aHeaders[$sName];
            }
            return false;
        }

        /**
         * Returns content.
         *
         * @return Content
         */
        public function getContent()
        {
            return $this->oContent;
        }

        /**
         * @return array|mixed|null
         * @throws Exception
         */
        private function getParamsFromJsonBody()
        {

            if ($this->hasContent() && $this->getContent()->getContentType()->isJson()) {
                $aJsonData = $this->getContent()->decodeAsJson(true);
                if (is_null($aJsonData)) {
                    // UNPROCESSABLE_ENTITY means content is not valid entity
                    // that could be parsed.
                    throw new Exception(
                        new Label('Could not decode json request.'),
                        Status::UNPROCESSABLE_ENTITY
                    );
                }
                return $aJsonData;
            }

            return null;

        }

        /**
         * @return null|\SimpleXMLElement
         * @throws Exception
         */
        private function getParamsFromXmlBody()
        {

            if ($this->hasContent() && $this->getContent()->getContentType()->isXml()) {
                try {
                    $oXml = $this->getContent()->getAsSimpleXml();
                } catch (\Exception $e) {

                    // UNPROCESSABLE_ENTITY means content is not valid entity
                    // that could be parsed.
                    throw new Exception(
                        new Label($e->getMessage()),
                        Status::UNPROCESSABLE_ENTITY
                    );

                }
                return $oXml;
            }

            return null;

        }

        /**
         * @param $sVariableName
         * @return array|mixed|null
         * @throws Exception
         * @throws \AtomPie\Web\Exception
         */
        public function getParamWithFallbackToBody($sVariableName)
        {

            $aRequestParams = $this->getAllParams()->getAll();

            // FallBack to JSON
            // If We can not find param in request
            // Maybe it is encoded as json in request body
            if (!$this->hasParam($sVariableName)) {
                $aRequestParams = $this->getParamsFromJsonBody();
            }

            // FallBack to XML
            // If We can not find param in request
            // Maybe it is encoded as XML in request body
            if (!isset($aRequestParams[$sVariableName])) {
                $oXml = $this->getParamsFromXmlBody();
                if (isset($oXml->{$sVariableName})) {
                    $sJson = json_encode($oXml);
                    $aRequestParams = json_decode($sJson, true);
                }
            }

            return isset($aRequestParams[$sVariableName])
                ? $aRequestParams[$sVariableName]
                : null;
        }

    }

}
