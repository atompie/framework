<?php
namespace AtomPie\Web\Connection\Http\Header {

    use AtomPie\Web\Connection\Http\Header;

    class Authorization extends Header
    {

        private $sAuthenticationType;
        private $sToken;

        public function __construct($sValue)
        {

            parent::__construct(Header::AUTHORIZATION, $sValue);
            $aHeaderParts = explode(' ', $sValue);

            if (isset($aHeaderParts[0])) {
                $this->sAuthenticationType = $aHeaderParts[0];
            }

            if (isset($aHeaderParts[1])) {
                $this->sToken = $aHeaderParts[1];
            }
        }

        /**
         * @return string
         */
        public function getAuthenticationType()
        {
            return $this->sAuthenticationType;
        }

        /**
         * @return string
         */
        public function getToken()
        {
            return $this->sToken;
        }

        /**
         * @param $sAuthType
         * @return bool
         */
        public function isAuth($sAuthType)
        {
            return strtolower($this->sAuthenticationType) == strtolower($sAuthType);
        }

    }

}
