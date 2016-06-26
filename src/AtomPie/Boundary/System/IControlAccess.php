<?php
namespace AtomPie\Boundary\System {

    interface IControlAccess
    {
        /**
         * Returns true if access is granted.
         *
         * @return bool
         */
        public function authorize();

        /**
         * Handle not authorized scenario. Throw Exception if you need to
         * stop dispatch process.
         *
         * @return void
         */
        public function invokeNotAuthorized();
    }
}