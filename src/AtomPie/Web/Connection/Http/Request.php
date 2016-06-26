<?php
namespace AtomPie\Web\Connection\Http {

    use AtomPie\I18n\Label;
    use AtomPie\System\IO\File;
    use AtomPie\System\IO\File\Permission\Exception as PermissionException;
    use AtomPie\Web\Boundary\IChangeRequest;
    use AtomPie\Web\Connection\Http\Header\ContentType;
    use AtomPie\Web\Connection\ProxyCredentials;
    use AtomPie\Web\Cookie;
    use AtomPie\Web\CookieJarFactory;
    use AtomPie\Web\Exception;
    use AtomPie\Web\SSL\PeerVerification;
    use AtomPie\Web\Connection\Http\Header;
    use AtomPie\Web\CookieJar;

    /**
     * Responsible for handling requests.
     */
    class Request extends ImmutableRequest implements IChangeRequest
    {

        /**
         * @var string
         */
        public $UserName;
        /**
         * @var string
         */
        public $Password;

        private $bIncludeHeaderInOutput = true;
        private $rOutputFile;
        private $rHeaderFile;
        private $sOutputFileName;

        public function saveOutputToFile($sFileName)
        {
            $this->sOutputFileName = $sFileName;
        }

        ////////////////////////
        // Method

        /**
         * Sets method. use \Web\Connection\Http\Request::GET,
         *                    \Web\Connection\Http\Request::POST,
         *                    \Web\Connection\Http\Request::PUT,
         *                    \Web\Connection\Http\Request::DELETE
         *
         * @param string $sMethod
         */
        public function setMethod($sMethod)
        {
            $this->__setMethod($sMethod);
        }

        /////////////////////////
        // Url

        /**
         * Sets \Web\Connection\Http\Url.
         *
         * @param Url $oHttpUrl
         */
        public function setUrl(Url $oHttpUrl)
        {
            $this->oHttpUrl = $oHttpUrl;
        }

        /**
         * Sets request time out.
         *
         * @param int $iTimeOut
         */
        public function setTimeOut($iTimeOut)
        {
            $this->iTimeOut = $iTimeOut;
        }

        /**
         * @param $sContentType
         * @throws Exception
         */
        public function setContentType($sContentType)
        {
            if (!$this->hasContent()) {
                throw new Exception(new Label('Content is not set.'));
            }
            $this->getContent()->setContentType(new ContentType($sContentType));
        }


        /**
         * @param $sKey
         */
        public function removeParam($sKey)
        {
            $this->oGetRequestCollection->remove($sKey);
            $this->oPostRequestCollection->remove($sKey);
            $this->oRequestCollection->remove($sKey);
        }

        /**
         * @param string $sKey
         * @param string $sValue
         * @param null $sMethod
         * @throws Exception
         */
        public function setParam($sKey, $sValue, $sMethod = null)
        {

            if ($sMethod == Request::GET) {
                $this->oGetRequestCollection->add($sValue, $sKey);
            }

            if ($sMethod == Request::POST) {
                $this->oPostRequestCollection->add($sValue, $sKey);
            }

            $this->oRequestCollection->add($sValue, $sKey);
        }


        public function setSSLPeerVerification(PeerVerification $oSslPeerVerificationData)
        {
            $this->oSslPeerVerification = $oSslPeerVerificationData;
        }

        /**
         * Sends request to \Web\Connection\Http\Url.
         *
         * @see setUrl() method
         * @param null $sUrl
         * @param null $aParams
         * @return Response
         * @throws Exception
         * @throws \Exception
         */
        public function send($sUrl = null, $aParams = null)
        {

            if (!is_null($sUrl)) {
                $oUrl = new Url($sUrl);

                if (!is_null($aParams)) {
                    $oUrl->setParams($aParams);
                }

                $this->setUrl($oUrl);
            }

            if (!$this->hasUrl()) {
                throw new Exception('Url is not set.');
            }

            $hCurlHandle = \curl_init();

            try {

                if ($this->hasProxy()) {
                    curl_setopt($hCurlHandle, CURLOPT_PROXY, $this->getProxy());
                    if ($this->hasProxyCredentials()) {
                        $oCredentials = $this->getProxyCredentials();
                        curl_setopt($hCurlHandle, CURLOPT_PROXYUSERPWD,
                            $oCredentials->Login . ':' . $oCredentials->Password);
                    }
                } else {
                    curl_setopt($hCurlHandle, CURLOPT_PROXY, null);
                }

                // INFO http://unitstep.net/blog/2009/05/05/using-curl-in-php-to-access-https-ssltls-protected-sites/
                if (isset($this->oSslPeerVerification)) {

                    curl_setopt($hCurlHandle, CURLOPT_SSL_VERIFYPEER, $this->oSslPeerVerification->SSlVerifyPeer);

                    if ($this->oSslPeerVerification->SSlVerifyPeer) {
                        curl_setopt($hCurlHandle, CURLOPT_SSL_VERIFYHOST, $this->oSslPeerVerification->SSlVerifyHost);
                        curl_setopt($hCurlHandle, CURLOPT_CAINFO, $this->oSslPeerVerification->CaInfo);
                    } else {
                        curl_setopt($hCurlHandle, CURLOPT_SSL_VERIFYHOST, false);
                    }

                }

                switch (strtolower($this->getMethod())) {
                    case Request::GET:
                        return $this->executeGet($hCurlHandle);
                    case Request::POST:
                        return $this->executePost($hCurlHandle);
                    case Request::PUT:
                        return $this->executePut($hCurlHandle);
                    case Request::DELETE:
                        return $this->executeDelete($hCurlHandle);
                    default:
                        throw new Exception('Current method (' . $this->getMethod() . ') is an invalid REST verb.');
                }
            } catch (\Exception $oEx) {

                curl_close($hCurlHandle);
                throw $oEx;

            }
        }


        /**
         * Execute CURL.
         *
         * @param $hCurlHandle
         * @throws Exception
         * @throws PermissionException
         * @throws TimeOutException
         * @return Response
         */
        private function doExecute(&$hCurlHandle)
        {

            if ($this->UserName !== null && $this->Password !== null) {
                curl_setopt($hCurlHandle, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
                curl_setopt($hCurlHandle, CURLOPT_USERPWD, $this->UserName . ':' . $this->Password);
            }

            curl_setopt($hCurlHandle, CURLOPT_TIMEOUT, $this->getTimeOut());
            curl_setopt($hCurlHandle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($hCurlHandle, CURLINFO_HEADER_OUT, true);
            curl_setopt($hCurlHandle, CURLOPT_ENCODING, 1);

            if (!empty($this->sOutputFileName)) {

                $rFileOpen = fopen($this->sOutputFileName, 'w+');
                if (false === $rFileOpen) {
                    $aError = error_get_last();
                    throw new PermissionException($aError['message']);
                }
                $this->rOutputFile = $rFileOpen;

                $rHeaderFileOpen = fopen($this->sOutputFileName . '.header', 'w+');
                if (false === $rHeaderFileOpen) {
                    $aError = error_get_last();
                    throw new PermissionException($aError['message']);
                }
                $this->rHeaderFile = $rHeaderFileOpen;

                curl_setopt($hCurlHandle, CURLOPT_FILE, $this->rOutputFile); // Write curl response to file
                curl_setopt($hCurlHandle, CURLOPT_WRITEHEADER, $this->rHeaderFile); // Write curl header to file
                curl_setopt($hCurlHandle, CURLOPT_FOLLOWLOCATION, true);
                $this->bIncludeHeaderInOutput = false;

            }

            curl_setopt($hCurlHandle, CURLOPT_HEADER, $this->bIncludeHeaderInOutput);

            // Content-type /////////////////////////////

            if ($this->hasContent() && !$this->hasHeader('Content-Type')) {
                $this->addHeader('Content-Type', $this->getContent()->getContentType());
            }

            // Cookie ///////////////////////////////////

            if ($this->hasCookies()) {
                $oCookiesJar = $this->getCookies();
                $oCookieJarManager = new CookieJarFactory($oCookiesJar);
                $sCookiesString = $oCookieJarManager->getMergedAllCookiesIntoOne();

                // TODO: this is not perfect solution but the only one that manages multiple cookies.
                // It loses information from path,domain, security, etc.

                // Must implode all cookies in one string
                curl_setopt($hCurlHandle, CURLOPT_COOKIE, $sCookiesString);
                // Overrides old cookie
                $this->addHeader('Cookie', $sCookiesString);
            }

            // Request Headers //////////////////////////

            if ($this->hasHeaders()) {
                curl_setopt($hCurlHandle, CURLOPT_HTTPHEADER, $this->getHeaders());
            }

            $sSessionId = session_id();

            if (PHP_SAPI == "cli" || session_status() == PHP_SESSION_ACTIVE) {  // CLI or no session
                // Send
                $sResponseBody = curl_exec($hCurlHandle);
            } else {

                // Close and open session after CURL exec due to lock on session when
                // sent to the same server

                session_write_close();
                // Send
                $sResponseBody = curl_exec($hCurlHandle);
                session_start();
                session_id($sSessionId);
            }

            if ($sResponseBody === false) {
                $iErrorNo = curl_errno($hCurlHandle);
                if ($iErrorNo == 28) {
                    throw new TimeOutException('CURL timed out the connection to server with method [' . $this->getMethod() . '] and headers [' . implode("\n",
                            $this->getHeaders()) . ']. Returned error message: ' . curl_error($hCurlHandle));
                }
                throw new Exception('CURL Could not connect to server with method [' . $this->getMethod() . '] and headers [' . implode("\n",
                        $this->getHeaders()) . ']. Returned error message: ' . curl_error($hCurlHandle));
            }

            // Response /////////////////////////

            $aResponseInfo = curl_getinfo($hCurlHandle);

            // Is fetched to file?
            if (!empty($this->sOutputFileName)) {

                // Inny sposób procedowania wyniku który jest plikiem

                if (!empty($this->rOutputFile)) {
                    fclose($this->rOutputFile);
                }
                if (!empty($this->rHeaderFile)) {
                    fclose($this->rHeaderFile);
                }

                $sHeaderFile = $this->sOutputFileName . '.header';

                $oHeader = new File($sHeaderFile);
                $oBody = new File($this->sOutputFileName);

                $oContent = new Content($oBody->loadRaw(), new ContentType($aResponseInfo['content_type']));

                $sHeadersAsString = $oHeader->loadRaw();
                $oHeader->remove();

            } else {

                if ($this->bIncludeHeaderInOutput === true) {
                    $iHeaderSize = $aResponseInfo['header_size'];
                    // Remove header from response
                    $sHeadersAsString = substr($sResponseBody, 0, $iHeaderSize);
                    $sBody = substr($sResponseBody, $iHeaderSize);
                } else {
                    $sHeadersAsString = '';
                    $sBody = $sResponseBody;
                }

                $oContent = new Content($sBody, new ContentType($aResponseInfo['content_type']));
            }

            list($aHeaders, $oStatus) = $this->parseHeaders($sHeadersAsString);

            // No status header in $sHeadersAsString
            if ($oStatus === null) {
                $oStatus = new Header\Status($aResponseInfo['http_code']);
            }

            /////////////////////////////
            // Create response

            $oResponse = $this->createResponse(
                $oStatus,
                $oContent,
                $aHeaders,
                $sHeadersAsString
            );

            curl_close($hCurlHandle);

            return $oResponse;
        }

        private function parseHeaders($sHeader)
        {

            $aHeaders = [];
            $oStatus = null;

            if (empty($sHeader)) {
                return $aHeaders;
            }

            $aMultipleHeaderSection = explode("\r\n\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', trim($sHeader)));

            if ($aMultipleHeaderSection > 1) {
                $sHeader = end($aMultipleHeaderSection);
            } else {
                $sHeader = current($aMultipleHeaderSection);
            }

            $aFields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $sHeader));

            foreach ($aFields as $sField) {
                if (preg_match('/([^:]+): (.+)/m', $sField, $aMatch)) {
                    $aMatch[1] = preg_replace_callback(
                        '/(?<=^|[\x09\x20\x2D])./',
                        function ($aMatches) {
                            return strtoupper($aMatches[0]);
                        },
                        strtolower(trim($aMatch[1]))
                    );
                    if (isset($aHeaders[$aMatch[1]])) {
                        $aHeaders[$aMatch[1]] = array($aHeaders[$aMatch[1]], $aMatch[2]);
                    } else {
                        $aHeaders[$aMatch[1]] = trim($aMatch[2]);
                    }
                } else {
                    if (preg_match('/(HTTP\/[0-9].[0-9]) ([0-9]{3}) (.+)/m', $sField, $aMatch)) {
                        $oStatus = new Header\Status($aMatch[2], $aMatch[3], $aMatch[1]);
                    }
                }
            }
            return [$aHeaders, $oStatus];
        }

        /**
         * Executes GET send.
         *
         * @param $hCurlHandle
         * @return Response
         */
        private function executeGet(&$hCurlHandle)
        {
            // If no params in url pass params from loaded request
            if (!$this->getUrl()->hasParams()) {
                $oParams = $this->getAllParams();
                if (!is_null($oParams)) {
                    $this->getUrl()->setParams($oParams->getAll());
                }
            }

            $this->bIncludeHeaderInOutput = true;
            curl_setopt($hCurlHandle, CURLOPT_URL, $this->getUrl()->__toString());

            return $this->doExecute($hCurlHandle);
        }

        /**
         * Executes POST send.
         *
         * @param $hCurlHandle
         * @return Response
         * @throws Exception
         * @throws PermissionException
         * @throws TimeOutException
         */
        private function executePost(&$hCurlHandle)
        {

            $this->bIncludeHeaderInOutput = true;
            curl_setopt($hCurlHandle, CURLOPT_POST, true);
            curl_setopt($hCurlHandle, CURLOPT_URL, $this->getUrl()->getUrl());
            curl_setopt($hCurlHandle, CURLOPT_BINARYTRANSFER, true);

            if (!$this->hasContent()) {

                // Get content from params as multipart/form-data or application/x-www-form-urlencoded
                list($aParams, $sContentType) = $this->makeContentFromParams();

                $this->addHeader(Header::CONTENT_TYPE, $sContentType);
                curl_setopt($hCurlHandle, CURLOPT_POSTFIELDS, $aParams);

                return $this->doExecute($hCurlHandle);

            }

            $oContent = $this->getContent();

            // Post content is reference to file not multipart/form-data
            // just bare file object

            if ($oContent->get() instanceof File) {

                $oFile = $oContent->get();

                /* @var $oFile File */

                if (!$oFile->isValid()) {
                    throw new Exception("Can not post file that does not exist!");
                }

                $hFile = fopen($oFile->getPath(), 'rb');

                curl_setopt($hCurlHandle, CURLOPT_INFILE, $hFile);
                curl_setopt($hCurlHandle, CURLOPT_INFILESIZE, $oFile->getSize());

                $oResponse = $this->doExecute($hCurlHandle);

                fclose($hFile);

                return $oResponse;

            } else {

                // Content is raw string or binary set as content.

                curl_setopt($hCurlHandle, CURLOPT_POSTFIELDS, $oContent->get());

                return $this->doExecute($hCurlHandle);

            }
        }

        /**
         * Executes PUT send.
         *
         * @param $hCurlHandle
         * @throws Exception
         * @return Response
         */
        private function executePut(&$hCurlHandle)
        {

            $mContent = $this->getContent();

            $this->bIncludeHeaderInOutput = true;
            curl_setopt($hCurlHandle, CURLOPT_URL, $this->getUrl());
            curl_setopt($hCurlHandle, CURLOPT_PUT, true);

            if ($mContent->get() instanceof File) {

                $oFile = $mContent->get();

                /* @var $oFile File */

                if (!$oFile->isValid()) {
                    throw new Exception("Can not put file that does not exist!");
                }

                $hFile = fopen($oFile->getPath(), 'rb');

                curl_setopt($hCurlHandle, CURLOPT_BINARYTRANSFER, true);
                curl_setopt($hCurlHandle, CURLOPT_INFILE, $hFile);
                curl_setopt($hCurlHandle, CURLOPT_INFILESIZE, $oFile->getSize());

                $oResponse = $this->doExecute($hCurlHandle);

                fclose($hFile);

            } else {

                $iRequestLength = strlen($this->getContent());

                $hMemory = fopen('php://memory', 'rw');
                fwrite($hMemory, $mContent);
                rewind($hMemory);

                curl_setopt($hCurlHandle, CURLOPT_INFILE, $hMemory);
                curl_setopt($hCurlHandle, CURLOPT_INFILESIZE, $iRequestLength);


                $oResponse = $this->doExecute($hCurlHandle);

                fclose($hMemory);
            }

            return $oResponse;
        }

        /**
         * Executes DELETE send.
         *
         * @param $hCurlHandle
         * @return Response
         */
        private function executeDelete(&$hCurlHandle)
        {

            $this->bIncludeHeaderInOutput = true;
            curl_setopt($hCurlHandle, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($hCurlHandle, CURLOPT_URL, $this->getUrl()->getUrl());

            return $this->doExecute($hCurlHandle);
        }

        ////////////////////////
        // IHandleReferrer

        /**
         * @param string $sReferrerUrl
         */
        public function setReferrerUrl($sReferrerUrl)
        {
            $this->sReferrerUrl = $sReferrerUrl;
        }

        /////////////////////////
        // IHandleProxy

        public function removeProxy()
        {
            $this->sProxy = null;
        }

        /**
         * @param string $sProxy
         * @param ProxyCredentials $oCredentials
         */
        public function setProxy($sProxy, ProxyCredentials $oCredentials = null)
        {
            $this->sProxy = $sProxy;
            $this->oCredentials = $oCredentials;
        }

        ////////////////////////
        // Request String


        /**
         * @param string $sRequestString
         */
        public function setRequestString($sRequestString)
        {
            $this->__setRequestString($sRequestString);
        }

        ////////////////////////
        // Cookie

        /**
         * @param \AtomPie\Web\CookieJar $oMyCookieJar
         */
        public function appendCookieJar(CookieJar $oMyCookieJar)
        {
            $oCookieJar = $this->getCookies();
            foreach ($oMyCookieJar->getAll() as $oCookie) {
                /* @var $oCookie Cookie */
                $oCookieJar->add($oCookie);
            }
        }

        /**
         * @param Cookie $oCookie
         */
        public function addCookie(Cookie $oCookie)
        {
            CookieJar::getInstance()->add($oCookie);
        }

        ////////////////////////
        // Header

        public function setHeadersSize($iHeadersSize)
        {
            $this->iHeadersSize = $iHeadersSize;
        }

        /**
         * Adds header to request.
         *
         * @param string $sName
         * @param string | Header $sValue
         */
        public final function addHeader($sName, $sValue)
        {
            $this->__addHeader($sName, $sValue);
        }

        public final function resetHeaders()
        {
            $this->aHeaders = array();
        }

        /**
         * @param string $sName
         */
        public final function removeHeader($sName)
        {
            $sName = strtoupper($sName);
            unset($this->aHeaders[$sName]);
        }


        ////////////////////////
        // Data
        /**
         * Sets content and adds json content-type header.
         * Remember to set method to POST, DELETE or PUT
         *
         * @param mixed $mContent
         * @return $this
         */
        public function setJsonContent($mContent)
        {
            $this->oContent = new Content($mContent, new ContentType(ContentType::JSON));
            return $this;
        }

        /**
         * Sets content and adds xml content-type header.
         *
         * @param mixed $mContent
         */
        public function setXmlContent($mContent)
        {
            $this->oContent = new Content($mContent, new ContentType(ContentType::XML));
        }

        /**
         * Sets content.
         *
         * @param Content $oContent
         */
        public function setContent(Content $oContent)
        {
            $this->__setContent($oContent);
        }

        private function makeContentFromParams()
        {
            $aParams = $this->getUrl()->getParams();
            $sContentType = 'application/x-www-form-urlencoded';

            if (is_array($aParams) && !empty($aParams)) {

                foreach ($aParams as $sParamName => $mParamValue) {
                    if ($mParamValue instanceof File) {

                        $sContentType = 'multipart/form-data';

                        // Function curl_file_create does not exists in php <5.5
                        if (!function_exists('curl_file_create')) {
                            function curl_file_create($sFilename, $sMimeType = '', $sPostName = '')
                            {
                                return "@$sFilename;filename="
                                . $sPostName
                                . ($sMimeType ? ";type=$sMimeType" : '');
                            }
                        }

                        // Upload file
                        $sFilePath = $mParamValue->getPath();
                        $aParams[$sParamName] = curl_file_create($sFilePath, '', $sParamName);
                    }
                }

            }

            return [$aParams, $sContentType];
        }

        /**
         * @param $oStatus
         * @param $oContent
         * @param $aHeaders
         * @param $sHeaders
         * @return Response
         */
        private function createResponse($oStatus, $oContent, array $aHeaders, $sHeaders)
        {
            $oResponse = new Response($oStatus);
            $oResponse->setContent($oContent);

            // Response Headers //////////////////////////

            foreach ($aHeaders as $sName => $sValue) {
                // Adds also set-cookie, but only one
                $oResponse->addHeader($sName, $sValue);
            }

            // Process if Set-Cookie header is returned
            $oCookieJar = CookieJarFactory::create($sHeaders);
            $oResponse->appendCookieJar($oCookieJar);
            return $oResponse;
        }

    }

}
