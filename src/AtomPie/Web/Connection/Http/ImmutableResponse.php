<?php
namespace AtomPie\Web\Connection\Http {

    use AtomPie\Web\Boundary\IAmResponse;
    use AtomPie\Web\Connection\Http\Header\Status;
    use AtomPie\Web\Cookie;
    use AtomPie\Web\CookieJar;

    class ImmutableResponse implements IAmResponse
    {

        /**
         * @var Header\Status
         */
        protected $oStatus;

        /**
         * @var Content
         */
        protected $oContent;

        /**
         * @var array
         */
        protected $aHeaders = array();

        /**
         * If $iStatus is null default status will be \Web\Connection\Http\Header\Status::OK.
         *
         * @param Status $oStatus
         */
        public function __construct(Status $oStatus = null)
        {
            if (is_null($oStatus)) {
                $oStatus = new Status(Status::OK);
            }
            $this->oStatus = $oStatus;
        }

        /**
         * Returns status.
         *
         * @return Header\Status
         */
        public final function getStatus()
        {
            return $this->oStatus;
        }

        /**
         * @param string $sName
         */
        protected function __removeHeader($sName)
        {
            $sName = strtoupper($sName);
            unset($this->aHeaders[$sName]);
        }

        /**
         * Adds header to request.
         *
         * @param string $sName
         * @param string | Header $mValue
         */
        protected function __addHeader($sName, $mValue)
        {
            $sName = strtoupper($sName);
            if ($mValue instanceof ImmutableHeader) {
                $this->aHeaders[$sName] = $mValue;
            } else {

                // Remove duplicates

                if (is_array($mValue)) {
                    $sValue = end($mValue);
                } else {
                    $sValue = $mValue;
                }

                // Check type

                if ($sName == ImmutableHeader::ACCEPT) {
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

        private function prepareHeaders()
        {

            $this->__addHeader(ImmutableHeader::STATUS, $this->oStatus);

            // Content-type from content has highest priority
            if ($this->hasContent()) {
                $oContentType = $this->getContent()->getContentType();
                $this->__addHeader($oContentType->getName(), $oContentType->getValue());
            }

            // Cookie - must be before headers
            if ($this->hasCookies()) {
                $this->__removeHeader(ImmutableHeader::SET_COOKIE); // Cookies will be sent from getCookies - only
                // There can be many set-cookie headers
                foreach ($this->getCookies()->getAll() as $oCookie) {
                    /* @var $oCookie Cookie */
                    $this->__addHeader(ImmutableHeader::SET_COOKIE, $oCookie->__toString());
                }

            }

        }

        /**
         * Sends response.
         */
        public function send()
        {

            // Prepare headers
            $this->prepareHeaders();

            // Prepare content
            $oContent = $this->getContent();

            if (PHP_SAPI != "cli") {

                // Send Headers
                foreach ($this->getHeaders() as $oHeader) {
                    if ($oHeader instanceof ImmutableHeader) {
                        $oHeader->send();
                    }
                }

            }

            echo $oContent->__toString();

            return;
        }

        public function redirect(Url $oUrl)
        {
            $this->__addHeader(ImmutableHeader::LOCATION, $oUrl->__toString());
            $this->send();
            exit;
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

        public function __toString()
        {

            // Prepare headers
            $this->prepareHeaders();

            $sHeader = implode("\n", $this->getHeaders());
            $sHeader .= "\n\n";
            $sHeader .= $this->getContent()->__toString();

            return $sHeader;
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
         * Returns content.
         *
         * @return Content
         */
        public function getContent()
        {
            return $this->oContent;
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

        protected function getRawData()
        {
            return file_get_contents('php://input');
        }
    }

}
