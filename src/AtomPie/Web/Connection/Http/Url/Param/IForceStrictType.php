<?php
namespace AtomPie\Web\Connection\Http\Url\Param {

    interface IForceStrictType
    {
        /**
         * This method must cast $this->Value to int, string, etc.
         * Return casted value it will replace original value of the parameter.
         *
         * @return mixed
         */
        public function castValue();
    }
}

