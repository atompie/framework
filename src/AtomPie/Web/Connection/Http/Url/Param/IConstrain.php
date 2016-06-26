<?php
namespace AtomPie\Web\Connection\Http\Url\Param {

    interface IConstrain
    {
        /**
         * This method can throw Exception. Value can be read from
         * $this->getValue(). Remember Value can be array or string.
         *
         * If you want to change response status from INTERNAL_SERVER_ERROR
         * @see \AtomPie\Web\Connection\Http\Status\Header pass code with
         * Exception.
         *
         * @return bool
         * @throws Constrain\Exception
         */
        public function validate();
    }
}