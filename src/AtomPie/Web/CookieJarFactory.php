<?php
namespace AtomPie\Web {

    class CookieJarFactory
    {
        /**
         * @var CookieJar
         */
        private $oCookieJar;

        /**
         * @param CookieJar $oCookieJar
         */
        public function __construct(CookieJar $oCookieJar)
        {
            $this->oCookieJar = $oCookieJar;
        }

        /**
         * @return string
         */
        public function getMergedAllCookiesIntoOne()
        {
            $aPairs = array();

            foreach ($this->oCookieJar->getAll() as $oCookie) {
                /* @var $oCookie Cookie */
                $aPairs[] = trim($oCookie->getName()) . '=' . trim($oCookie->getValue());
            }

            return implode('; ', $aPairs);
        }

        /**
         * @param string $sHeader
         * @return CookieJar
         */
        public static function create($sHeader)
        {

            $aCookies = self::parse($sHeader);

            $oCookieJar = CookieJar::getInstance();

            foreach ($aCookies as $aCookie) {
                if (isset($aCookie['cookies']) && is_array($aCookie['cookies'])) {
                    foreach ($aCookie['cookies'] as $sKey => $sValue) {

                        $oCookie = new Cookie(trim($sKey), trim($sValue));
                        if (isset($aCookie['expires'])) {
                            $oCookie->setExpire($aCookie['expires']);
                        }
                        if (isset($aCookie['path'])) {
                            $oCookie->setDomainPath($aCookie['path']);
                        }
                        if (isset($aCookie['domain'])) {
                            $oCookie->setDomain($aCookie['domain']);
                        }
                        if (isset($aCookie['secure']) && $aCookie['secure'] == true) {
                            $oCookie->setSecure(true);
                        }
                        if (isset($aCookie['httponly']) && $aCookie['httponly'] == true) {
                            $oCookie->setHttpOnly(true);
                        }

                        $oCookieJar->add($oCookie);
                    }
                }
            }

            return $oCookieJar;
        }

        public static function parse($sHeader)
        {

            $aCookies = array();
            $aHeader = explode("\n", $sHeader);
            foreach ($aHeader as $aLine) {

                if (preg_match('/^Set-Cookie:/i', $aLine)) {
                    $aLine = preg_replace('/^Set-Cookie:/i', '', trim($aLine));
                    $aContentSplit = explode(';', trim($aLine));
                    $aContentData = array();

                    foreach ($aContentSplit as $aData) {

                        $aContentInfo = explode('=', $aData);
                        $aContentInfo[0] = trim($aContentInfo[0]);

                        if ($aContentInfo[0] == 'expires') {
                            $aContentInfo[1] = @strtotime($aContentInfo[1]);
                        }

                        if ($aContentInfo[0] == 'secure') {
                            $aContentInfo[1] = "true";
                        }

                        if (strtolower($aContentInfo[0]) == 'httponly') {
                            $aContentInfo[1] = "true";
                        }

                        if (in_array($aContentInfo[0],
                            array('domain', 'expires', 'path', 'secure', 'comment', 'httponly'))) {
                            $aContentData[trim($aContentInfo[0])] = $aContentInfo[1];
                        } else {
                            $aContentData['cookies'][$aContentInfo[0]] = $aContentInfo[1];
                        }
                    }
                    $aCookies[] = $aContentData;

                }

            }

            return $aCookies;
        }
    }
}
