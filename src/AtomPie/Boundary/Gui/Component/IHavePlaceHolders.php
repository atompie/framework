<?php
namespace AtomPie\Boundary\Gui\Component {

    interface IHavePlaceHolders
    {

        /**
         * @return array
         */
        public function getPlaceHolders();

        /**
         * @return bool
         */
        public function hasPlaceHolders();
    }
}
