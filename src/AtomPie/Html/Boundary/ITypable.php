<?php
namespace AtomPie\Html\Boundary {

    interface ITypable
    {
        /**
         * @param $sType
         * @return void
         */
        public function setType($sType);

        /**
         * @return \Generi\Type
         */
        public function getType();
    }

}
