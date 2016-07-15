<?php
namespace AtomPie\Core\Config {

    use AtomPie\Boundary\Core\IAmApplicationConfigSwitcher;

    class ConfigSwitcher implements IAmApplicationConfigSwitcher
    {

        /**
         * @var string
         */
        private $sDefaultConfigClassName;
        
        /**
         * @var string
         */
        private $sLocalConfigClassName;

        /**
         * ConfigSwitcher constructor.
         * @param $sDefaultConfigClassName
         * @param null $sLocalConfigClassName
         */
        public function __construct($sDefaultConfigClassName, $sLocalConfigClassName = null)
        {
            $this->sDefaultConfigClassName = $sDefaultConfigClassName;
            $this->sLocalConfigClassName = $sLocalConfigClassName;
        }

        /**
         * Returns default config class. One that is used
         * if no class is returned by localConfig() method.
         *
         * @return string
         */
        public function defaultConfig() {
            return $this->sDefaultConfigClassName;
        }

        /**
         * Returns local config class to be used depending on
         * environment used. In order to change how the environment is
         * determined override provide method.
         *
         * @return string
         */
        public function localConfig() {
            return $this->sLocalConfigClassName;
        }

    }

}