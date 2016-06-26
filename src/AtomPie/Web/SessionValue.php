<?php
namespace AtomPie\Web {

    use Generi\NameValuePair;

    class SessionValue extends NameValuePair
    {
        public function __toString()
        {
            return $this->getValue();
        }
    }

}
