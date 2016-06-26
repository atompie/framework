<?php
namespace AtomPie\Web {

    use Generi\Object;
    use AtomPie\Web\Boundary\IAmSession;

    class Session extends Object implements IAmSession
    {

        /**
         * @var IAmSession
         */
        private static $oInstance;

        private $bIsSessionStarted = false;

        /**
         * @return $this
         */
        public static function getInstance()
        {

            if (!isset(self::$oInstance)) {
                self::$oInstance = new self();
            }

            return self::$oInstance;
        }

        public static function destroyInstance()
        {
            if (session_status() == PHP_SESSION_NONE) {
                session_commit();
            }
            self::$oInstance = null;
        }

        private function __construct()
        {
        }

        private function startSession()
        {
            if (!$this->bIsSessionStarted) {
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                    if (isset($_SESSION)) {
                        foreach ($_SESSION as $sKey => $sValue) {
                            $this->mergeKeyValue($sKey, $sValue);
                        }
                    }
                }
                $this->bIsSessionStarted = true;
            }
        }

        /**
         * @param $sKey
         * @return mixed | null
         */
        public function get($sKey)
        {
            $this->startSession();
            return isset($_SESSION[$sKey]) ? $_SESSION[$sKey] : null;
        }

        /**
         * @param $sKey
         * @param $mValue
         */
        public function mergeKeyValue($sKey, $mValue)
        {
            $this->startSession();
            if (!isset($_SESSION[$sKey])) {
                $_SESSION[$sKey] = $mValue;
            } else {
                if (is_array($mValue)) {
                    if (!is_array($_SESSION[$sKey])) {
                        $_SESSION[$sKey] = array();
                    }
                    // Merge
                    foreach ($mValue as $sName => $sValue) {
                        $_SESSION[$sKey][$sName] = $sValue;
                    }
                } else {
                    // Replace all other
                    $_SESSION[$sKey] = $mValue;
                }
            }
        }

        public function set($sKey, $mValue)
        {
            $this->startSession();
            $_SESSION[$sKey] = $mValue;
        }

        /**
         * @param $sKey
         */
        public function remove($sKey)
        {
            $this->startSession();
            unset($_SESSION[$sKey]);
        }

        /**
         * @param $sKey
         * @return bool
         */
        public function has($sKey)
        {
            $this->startSession();
            return isset($_SESSION[$sKey]);
        }

        /**
         * @return bool
         */
        public function isEmpty()
        {
            $this->startSession();
            return empty($_SESSION);
        }

        /**
         * @return array
         */
        public function getAll()
        {
            $this->startSession();
            return $_SESSION;
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
            $this->startSession();
            return count($_SESSION);
        }
    }

}