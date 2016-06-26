<?php
namespace AtomPie\Web {

    use Generi\NameValuePair;

    /**
     * Collection of cookies in jar.
     */
    class CookieJar
    {

        /**
         * @var CookieJar
         */
        private static $oInstance;

        public static function destroyInstance()
        {
            self::$oInstance = null;
        }

        /**
         * @todo remove
         * @return $this
         */
        public static function getInstance()
        {

            if (!isset(self::$oInstance)) {
                self::$oInstance = new self();
            }

            return self::$oInstance;
        }

        /**
         * @var array
         */
        private $aStorage = array();

        /**
         * @param $sName
         * @return NameValuePair
         */
        public function get($sName)
        {
            return $this->aStorage[$sName];
        }

        /**
         * @param NameValuePair $oCookie
         * @throws Exception
         */
        public function add(NameValuePair $oCookie)
        {
            if (!$oCookie instanceof Cookie) {
                throw new Exception('Only cookie object can be add to cookie jar.');
            }

            $this->aStorage[$oCookie->getName()] = $oCookie;
        }

        /**
         * @param $sName
         */
        public function remove($sName)
        {
            unset($this->aStorage[$sName]);
        }

        /**
         * @param $sName
         * @return bool
         */
        public function has($sName)
        {
            return isset($this->aStorage[$sName]);
        }

        /**
         * @return bool
         */
        public function isEmpty()
        {
            return empty($this->aStorage);
        }

        /**
         * @return array
         */
        public function getAll()
        {
            return $this->aStorage;
        }

        ///////////////////////////

        /**
         * (PHP 5 &gt;= 5.1.0)<br/>
         * Count elements of an object
         * @link http://php.net/manual/en/countable.count.php
         * @return int The custom count as an integer.
         * </p>
         * <p>
         * The return value is cast to an integer.
         */
        public function count()
        {
            return count($this->aStorage);
        }

        public function __toString()
        {
            return implode(PHP_EOL, $this->getAll());
        }

    }
}