<?php
namespace AtomPie\Web\Boundary {

    interface IAmRequestParam
    {

        public function __construct($sName, $sValue = null);

        /**
         * @return string
         */
        public function getName();

        /**
         * @return mixed
         */
        public function getValue();

        /**
         * @return bool
         */
        public function isArray();

        /**
         * @return bool
         */
        public function isNull();

        /**
         * @return bool
         */
        public function isEmpty();
    }

}
