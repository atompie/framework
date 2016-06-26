<?php
namespace AtomPie\Html\Boundary {

    interface ICheckable
    {
        /**
         * Check checkable form element.
         */
        public function check();

        /**
         * Uncheck checkable form element.
         */
        public function uncheck();

        /**
         * Is checked.
         *
         * @return bool
         */
        public function isChecked();

        /**
         * Is the value set in object the same ass passed value $sValue.
         *
         * @param string | array $mValue
         */
        public function equals($mValue);
    }

}
