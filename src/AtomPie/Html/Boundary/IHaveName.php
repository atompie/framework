<?php
namespace AtomPie\Html\Boundary {

    interface IHaveName
    {
        /**
         * @param string $sName
         */
        public function setName($sName);

        /**
         * @return string
         */
        public function getName();

        /**
         * @return boolean
         */
        public function hasName();
    }

}