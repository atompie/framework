<?php
namespace AtomPie\Web\Connection\Http {

    use AtomPie\I18n\Label;
    use AtomPie\Web\Boundary\IAmHeader;
    use AtomPie\Web\Exception;

    class ImmutableHeader implements IAmHeader
    {

        const STATUS = 'HTTP/1.1';
        const CONTENT_TYPE = 'CONTENT-TYPE';
        const ACCEPT = 'ACCEPT';
        const AUTHORIZATION = 'AUTHORIZATION';
        const SET_COOKIE = 'SET-COOKIE';
        const LOCATION = 'LOCATION';

        /**
         * @var string
         */
        private $sName;

        /**
         * @var string
         */
        protected $sValue;

        /**
         * Wrapper for header function.
         *
         * Usage:
         * <pre>
         * $oHeader = new Header(Header::LOCATION,'http://www.yahoo.com');
         * $oHeader->send();
         * </pre>
         *
         * @param string $sName
         * @param string $sValue
         * @throws Exception
         */
        public function __construct($sName, $sValue)
        {
            if (!is_string($sName)) {
                throw new Exception(new Label('Incorrect header name. Expected string value.'));
            }
            if (!is_string($sValue) and !is_numeric($sValue)) {
                throw new Exception(new Label('Incorrect header value. Expected string value.'));
            }
            $this->sValue = $sValue;
            $this->sName = $sName;
        }

        /**
         * Returns a name of a header, e.g. Location, Date, Accept, etc.
         * See http://en.wikipedia.org/wiki/List_of_HTTP_header_fields for header filed names.
         *
         * @return string $sName
         */
        public final function getName()
        {
            return $this->sName;
        }

        /**
         * @param $sName
         */
        protected function setName($sName)
        {
            $this->sName = $sName;
        }

        /**
         * Returns value of a header.
         *
         * @return string $sValue
         */
        public final function getValue()
        {
            return $this->sValue;
        }

        public function __toString()
        {
            return $this->prepareHeader();
        }

        /**
         * Prepares header string. This could be overrode if
         * different structure of header is needed. E.g. Status Header HTTP 1.1
         * has different information in it.
         *
         * @return string
         */
        protected function prepareHeader()
        {
            return $this->sName . ': ' . $this->sValue;
        }

        /**
         * Send a raw HTTP header
         * @link http://www.php.net/manual/en/function.header.php
         * @param string string <p>
         * The header string.
         * </p>
         * <p>
         * There are two special-case header calls. The first is a header
         * that starts with the string "HTTP/" (case is not
         * significant), which will be used to figure out the HTTP status
         * code to send. For example, if you have configured Apache to
         * use a PHP script to handle requests for missing files (using
         * the ErrorDocument directive), you may want to
         * make sure that your script generates the proper status code.
         * </p>
         * <p>
         * ]]>
         * </p>
         * <p>
         * The second special case is the "Location:" header. Not only does
         * it send this header back to the browser, but it also returns a
         * REDIRECT (302) status code to the browser
         * unless the 201 or
         * a 3xx status code has already been set.
         * </p>
         * <p>
         * ]]>
         * </p>
         * @param $bReplaceCurrent bool[optional] <p>
         * The optional replace parameter indicates
         * whether the header should replace a previous similar header, or
         * add a second header of the same type. By default it will replace,
         * but if you pass in false as the second argument you can force
         * multiple headers of the same type. For example:
         * </p>
         * <p>
         * ]]>
         * </p>
         * @param $iStatus int[optional] <p>
         * Forces the HTTP response code to the specified value.
         * </p>
         * @return void
         */
        public function send($bReplaceCurrent = null, $iStatus = null)
        {
            header($this->prepareHeader(), $bReplaceCurrent, $iStatus);
        }
    }

}
