<?php
namespace AtomPie\System\Environment {

    use AtomPie\Boundary\System\IAmEnvVariable;

    class EnvVariable implements IAmEnvVariable
    {

        private static $oInstance;

        /**
         * @return EnvVariable
         */
        public static function getInstance()
        {
            if (!isset(self::$oInstance)) {
                self::$oInstance = new self();
            }
            return self::$oInstance;
        }

        static public function destroyInstance()
        {
            self::$oInstance = null;
        }


        private function __construct()
        {
        }

        public function get($sName)
        {
            $sEnvValue = getenv($sName);
            return ($sEnvValue !== false) ?
                $sEnvValue
                : null;
        }

        public function has($sName)
        {
            $sVariable = $this->get($sName);
            return isset($sVariable);
        }

    }

}
