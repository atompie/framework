<?php
namespace AtomPie\Web\Connection\Http {

    use AtomPie\System\IO\File;
    use AtomPie\Web\Boundary\IAmContent;
    use AtomPie\Web\Boundary\IAmStatusHeader;
    use AtomPie\Web\Boundary\IChangeResponse;
    use AtomPie\Web\Connection\Http\Header\ContentType;
    use AtomPie\Web\Cookie;
    use AtomPie\Web\Connection\Http\Header;
    use AtomPie\Web\CookieJar;

    /**
     * Response class can be used to send response to the client request or
     * receive response if the the server send request to other system.
     *
     *
     * Example 1: Send request and receive response.
     *
     *  <pre>
     *  // Define url
     *  $oUrl = new \Web\Connection\Http\Url();
     *  $oUrl->setUrl('http://localhost/cont/action');
     *
     *  // Create request
     *    $request = new \Web\Connection\Http\Request();
     *  // Set method if not default GET
     *    $request->setMethod('post');
     *  // Set url
     *  $request->setUrl($oUrl);
     *  // Add additional headers
     *  $request->addHeader('Accept',\Web\Connection\Http\ContentType::JSON);
     *  // Add content (in this example JSON encoded content)
     *  $request->setJsonContent('{id:1}');
     *
     *  $request->addHeader('Warning', '001');
     *  // Api key
     *  $request->addHeader('Content-MD5', 'qwerty');
     *
     *  // Send - returns response object.
     *  $response = $request->send();
     *
     *  // Prints response status
     *  echo $response->getStatus();
     *  </pre>
     *
     * Example 2: Receive request and send response.
     *
     * <pre>
     *  // Receive request
     *  $request =  new \Web\Connection\Http\Request();
     *  $request->load();
     *  echo $request->getHeader('Accept');
     *
     *  // Process
     *
     *  // Send response
     *  $response =  new \Web\Connection\Http\Response(\Web\Connection\Http\Header\Status::OK);
     *  $response->setContent(new \Web\Connection\Http\Content('My response', new \Web\Connection\Http\ContentType('text/plain')));
     *  $response->addHeader('Custom', 'MyHeader');
     *  $response->send();
     *  </pre>
     */
    class Response extends ImmutableResponse implements IChangeResponse
    {

        /**
         * Sets status code.
         *
         * @param IAmStatusHeader $oStatus
         */
        public final function setStatus(IAmStatusHeader $oStatus)
        {
            $this->oStatus = $oStatus;
        }

        public function setContentType($sContentType)
        {
            $this->getContent()->setContentType(new ContentType($sContentType));
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
            $this->__removeHeader($sName);
        }

        /**
         * Sets content.
         *
         * @param IAmContent $oContent
         */
        public function setContent(IAmContent $oContent)
        {
            $this->oContent = $oContent;
            if ($oContent->hasContentType()) {
                $this->addHeader(Header::CONTENT_TYPE, $oContent->getContentType());
            }
        }

    }
}