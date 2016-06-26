<?php
namespace Workshop\FrontEnd\Param {

    use AtomPie\Web\Connection\Http\Url\Param;

    class BackUrl extends Param
    {

        public function getEncodedValue()
        {
            return base64_encode($this->getValue());
        }

        public function getDecodedValue()
        {
            return base64_decode($this->getValue());
        }

    }

}
 