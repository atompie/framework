<?php
namespace AtomPie\Html\Boundary {

    interface IValuePopulator
    {
        /**
         * Sets value for IMultiValues objects.
         *
         * @param array $aValues
         * @return void
         */
        public function __populateValue(array $aValues);
    }

}