<?php
namespace AtomPie\Web {

    use Generi\NameValuePair;

    class Cookie extends NameValuePair
    {

        private $iExpire;
        private $sDomainPath;
        private $sDomain;
        private $bSecure;
        private $bHttpOnly;

        /**
         * @param string $sKey
         * @param string $sValue
         * @param int $iExpire
         * @param string $sDomainPath
         * @param null $sDomain
         * @param bool $bSecure
         * @param bool $bHttpOnly
         */
        public function __construct(
            $sKey,
            $sValue = null,
            $iExpire = 0,
            $sDomainPath = '/',
            $sDomain = null,
            $bSecure = false,
            $bHttpOnly = false
        ) {
            parent::__construct($sKey, $sValue);
            $this->iExpire = $iExpire;
            $this->sDomainPath = $sDomainPath;
            $this->sDomain = $sDomain;
            $this->bSecure = $bSecure;
            $this->bHttpOnly = $bHttpOnly;
        }

        public function __toString()
        {

            $sCookie = parent::__toString() . '; Path=' . $this->sDomainPath;

            if (!empty($this->iExpire)) {
                $sCookie .= '; Expires=' . @date(DATE_COOKIE, time() + $this->iExpire);
            }
            if (isset($this->sDomain)) {
                $sCookie .= '; Domain=' . $this->sDomain;
            }
            if (isset($this->bSecure) && $this->bSecure == true) {
                $sCookie .= '; Secure';
            }
            if (isset($this->bHttpOnly) && $this->bHttpOnly == true) {
                $sCookie .= '; HttpOnly';
            }
            return $sCookie;
        }

        /**
         * @return int $iExpire
         */
        public function getExpire()
        {
            return $this->iExpire;
        }

        /**
         * @return string $sDomainPath
         */
        public function getDomainPath()
        {
            return $this->sDomainPath;
        }

        /**
         * @return string $sDomain
         */
        public function getDomain()
        {
            return $this->sDomain;
        }

        /**
         * @return bool $bSecure
         */
        public function getSecure()
        {
            return $this->bSecure;
        }

        /**
         * @return bool $bHttpOnly
         */
        public function getHttpOnly()
        {
            return $this->bHttpOnly;
        }

        /**
         * @param int $iExpire
         */
        public function setExpire($iExpire)
        {
            $this->iExpire = $iExpire;
        }

        /**
         * @param string $sDomainPath
         */
        public function setDomainPath($sDomainPath)
        {
            $this->sDomainPath = $sDomainPath;
        }

        /**
         * @param string $sDomain
         */
        public function setDomain($sDomain)
        {
            $this->sDomain = $sDomain;
        }

        /**
         * @param bool $bSecure
         */
        public function setSecure($bSecure)
        {
            $this->bSecure = $bSecure;
        }

        /**
         * @param bool $bHttpOnly
         */
        public function setHttpOnly($bHttpOnly)
        {
            $this->bHttpOnly = $bHttpOnly;
        }

        public function send()
        {
            setcookie($this->getName(), $this->getValue(), $this->iExpire, $this->sDomainPath, $this->sDomain,
                $this->bSecure, $this->bHttpOnly);
        }

    }
}
