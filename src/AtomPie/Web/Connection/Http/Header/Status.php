<?php
namespace AtomPie\Web\Connection\Http\Header {

    use AtomPie\Web\Boundary\IAmStatusHeader;
    use AtomPie\Web\Connection\Http\Header;
    use AtomPie\Web\Connection\Http\ImmutableHeader;
    use AtomPie\Web\Exception;

    /**
     * Responsible for sending response header with status of the operation.
     * Has set of constants - http statuses. Use defined constants to
     * refer response statuses.
     */
    class Status extends ImmutableHeader implements IAmStatusHeader
    {

        // Valid http statuses
        const CONT = 100;
        const SWITCHING_PROTOCOLS = 101;
        const CREATED = 201;
        const OK = 200;
        const ACCEPTED = 202;
        const NOT_AUTHORITATIVE_INFORMATION = 203;
        const NO_CONTENT = 204;
        const RESET_CONTENT = 205;
        const PARTIAL_CONTENT = 206;
        const MULTIPLE_CHOICES = 300;
        const MOVED_PERMANENTLY = 301;
        const FOUND = 302;
        const SEE_OTHER = 303;
        const NOT_MODIFIED = 304;
        const USE_PROXY = 305;
        const TEMPORARY_REDIRECT = 306;
        const BAD_REQUEST = 400;
        const UNAUTHORIZED = 401;
        const PAYMENT_REQUIRED = 402;
        const FORBIDDEN = 403;
        const NOT_FOUND = 404;
        const METHOD_NOT_ALLOWED = 405;
        const NOT_ACCEPTABLE = 406;
        const UNSUPPORTED_MEDIA_TYPE = 415;
        const EXPECTATION_FAILED = 417;
        const UNPROCESSABLE_ENTITY = 422;
        const INTERNAL_SERVER_ERROR = 500;

        /**
         *
         * Status codes and status descriptions.
         *
         * @var array
         */
        private static $aCodes = array(
            self::CONT => 'Continue',
            self::SWITCHING_PROTOCOLS => 'Switching Protocols',
            self::OK => 'OK',
            self::CREATED => 'Created',
            self::ACCEPTED => 'Accepted',
            self::NOT_AUTHORITATIVE_INFORMATION => 'Non-Authoritative Information',
            self::NO_CONTENT => 'No Content',
            self::RESET_CONTENT => 'Reset Content',
            self::PARTIAL_CONTENT => 'Partial Content',
            self::MULTIPLE_CHOICES => 'Multiple Choices',
            self::MOVED_PERMANENTLY => 'Moved Permanently',
            self::FOUND => 'Found',
            self::SEE_OTHER => 'See Other',
            self::NOT_MODIFIED => 'Not Modified',
            self::USE_PROXY => 'Use Proxy',
            306 => '(Unused)',
            self::TEMPORARY_REDIRECT => 'Temporary Redirect',
            self::BAD_REQUEST => 'Bad Request',
            self::UNAUTHORIZED => 'Unauthorized',
            self::PAYMENT_REQUIRED => 'Payment Required',
            self::FORBIDDEN => 'Forbidden',
            self::NOT_FOUND => 'Not Found',
            self::METHOD_NOT_ALLOWED => 'Method Not Allowed',
            self::NOT_ACCEPTABLE => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            self::UNPROCESSABLE_ENTITY => 'Unprocessable Entity',
            self::EXPECTATION_FAILED => 'Expectation Failed',
            self::INTERNAL_SERVER_ERROR => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported'
        );

        /**
         * @var string
         */
        private $sMessage;

        /**
         * @var string
         */
        private $sVersion;

        /**
         * Sets response status. Throws Exception if $iStatus is invalid.
         *
         * @param int $iStatus
         * @param string $sMessage
         * @param string $sVersion
         * @throws \Exception
         * @throws \AtomPie\Web\Exception
         */
        public function __construct($iStatus, $sMessage = '', $sVersion = 'HTTP/1.1')
        {
            if (!self::isValidStatusCode($iStatus) or !is_numeric($iStatus)) {
                throw new Exception('Invalid [' . $iStatus . '] http status!');
            }

            $this->sMessage = $sMessage;
            $this->sVersion = $sVersion;

            parent::__construct($this->sVersion, (int)$iStatus);
        }

        /**
         * @see Web\Connection\Http\Header::send()
         * @param null $bReplaceCurrent
         * @param null $iStatus
         */
        public function send($bReplaceCurrent = null, $iStatus = null)
        {
            http_response_code($this->getValue());
        }

        /**
         * @return string
         */
        public function getMessage()
        {
            return $this->sMessage;
        }

        /**
         * @return string
         */
        public function getVersion()
        {
            return $this->sVersion;
        }

        /**
         * @see Web\Connection\Http\Header::prepareHeader()
         */
        protected function prepareHeader()
        {
            return $this->getName() . ' ' . $this->getValue() . ' ' . $this->getStatusCodeMessage($this->getValue());
        }

        /**
         * Returns message for valid http status code.
         *
         * @param int $iStatus Status code
         * @return string
         */
        private function getStatusCodeMessage($iStatus)
        {
            if (!empty($this->sMessage)) {
                return $this->sMessage;
            }
            return (self::isValidStatusCode($iStatus)) ? self::$aCodes[$iStatus] : '';
        }

        /**
         * Returns true if $iStatus is a valid http status code.
         *
         * @param int $iStatus
         * @return bool
         */
        public static function isValidStatusCode($iStatus)
        {
            return isset(self::$aCodes[$iStatus]);
        }

        public function is($iStatusCode)
        {
            return $this->sValue == $iStatusCode;
        }

        /**
         * @return bool
         */
        public function isServerError()
        {
            return (int)$this->sValue >= 500 and (int)$this->sValue <= 599;
        }

        /**
         * @return bool
         */
        public function isResourceError()
        {
            return (int)$this->sValue >= 400 and (int)$this->sValue <= 499;
        }

        /**
         * @return bool
         */
        public function isRedirect()
        {
            return (int)$this->sValue >= 300 and (int)$this->sValue <= 399;
        }

        /**
         * @return bool
         */
        public function isOk()
        {
            return (int)$this->sValue >= 200 and (int)$this->sValue <= 299;
        }
    }
}

