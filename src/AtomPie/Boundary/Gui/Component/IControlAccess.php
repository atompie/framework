<?php
namespace AtomPie\Boundary\Gui\Component {

    interface IControlAccess
    {
        /**
         * ////////// IControlAccess //////////////
         *
         * Returns true if access is granted.
         *
         * @return bool
         */
        public function authorize();

        /**
         * ////////// IControlAccess //////////////
         *
         * Handle not authorized scenario. Throw Exception if you need to
         * stop dispatch process.
         *
         * @return void
         */
        public function invokeNotAuthorized();
    }
}