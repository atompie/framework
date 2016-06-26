<?php
namespace AtomPie\Web\Connection\Http {

    use Generi\Boundary\IStringable;
    use Generi\Object;
    use AtomPie\I18n\Label;
    use AtomPie\Web\Boundary\IAmImmutableUrl;
    use AtomPie\Web\Boundary\IChangeRequest;
    use AtomPie\Web\Exception;

    /**
     * Value object of URL.
     */
    class ImmutableUrl extends Object implements IStringable, IAmImmutableUrl
    {

        /**
         * @var string[]
         */
        protected $aParams = array();
        /**
         * @var string
         */
        protected $sUrl;

        /**
         * @var string
         */
        protected $sAnchor;

        const PARAM_SEPARATOR = '&';
        const URL_SEPARATOR = '?';

        /**
         * New \Web\Connection\Http\Url objects will has url set as PHP_SELF and params set from $_GET, $POST.
         * @param null $sUrl
         * @param null $aParams
         * @throws Exception
         */
        public function __construct($sUrl = null, $aParams = null)
        {

            if ($sUrl instanceof IStringable) {
                $sUrl = $sUrl->__toString();
            }

            if (!is_string($sUrl)) {
                throw new Exception('Url must be string.');
            }

            $this->aParams = is_null($sUrl) ? array_merge($_GET, $_POST) : $aParams;
            $this->sUrl = is_null($sUrl) ? $_SERVER['PHP_SELF'] : $sUrl;
        }

        /**
         * Returns $aParams as key1=value1&key2=value
         *
         * @param IChangeRequest | array $mParams
         * @return string
         * @throws Exception
         */
        public static function getParamsAsString($mParams)
        {
            if (is_array($mParams)) {
                return http_build_query($mParams, null, ImmutableUrl::PARAM_SEPARATOR);
            } else {
                if ($mParams instanceof IChangeRequest) {
                    return http_build_query($mParams->getAllParams()->getAll(), null, ImmutableUrl::PARAM_SEPARATOR);
                }
            }

            throw new Exception(new Label('Invalid parameters.'));
        }

        /**
         * Returns url as a string. Without server address.
         *
         * @return string|NULL
         */
        public function getRequestString()
        {
            if ($this->hasParams()) {
                return self::getParamsAsString($this->aParams);
            }
            return null;
        }

        public function getAnchor()
        {
            return $this->sAnchor;
        }

        public function hasAnchor()
        {
            return isset($this->sAnchor);
        }

        /**
         * Returns Url as a string.
         *
         * @return string
         */
        public function __toString()
        {
            if (!$this->hasParams()) {
                return $this->sUrl;
            }

            $sUrl = $this->sUrl . ImmutableUrl::URL_SEPARATOR . $this->getRequestString();

            if ($this->hasAnchor()) {
                return $sUrl . '#' . $this->getAnchor();
            }

            return $sUrl;
        }

        /**
         * Returns \Web\Connection\Http\Url parameters as array.
         *
         * @return array $aParams
         */
        public function getParams()
        {
            return $this->aParams;
        }

        /**
         * Returns true if \Web\Connection\Http\Url has parameters.
         *
         * @return bool
         */
        public function hasParams()
        {
            return !empty($this->aParams);
        }

        /**
         * @param string $sParamName
         * @return string
         */
        public function getParam($sParamName)
        {
            return $this->aParams[$sParamName];
        }

        //////////////////////
        // IAmImmutableUrl

        /**
         * Returns Url string.
         *
         * @return string $sUrl
         */
        public function getUrl()
        {
            return $this->sUrl;
        }

        /**
         * Returns TRUE if request has set URL, FALSE if oposit.
         *
         * @return bool
         */
        public function hasUrl()
        {
            return isset($this->sUrl);
        }
    }

}
