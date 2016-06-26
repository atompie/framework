<?php
namespace AtomPie\Boundary\Core {

    interface IAmApplicationConfigDefinition
    {
        /**
         * Returns default config class type. One that is used
         * if no class is returned by getLocalConfig() method.
         *
         * @return string
         */
        public function defaultConfig();

        /**
         * Returns local config class to be used depending on
         * environment used. In order to change how the environment is
         * determined override provide method.
         *
         * @return string
         */
        public function getLocalConfig();
    }

}

