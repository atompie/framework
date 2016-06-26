<?php
namespace AtomPie\I18n {

    class Number
    {

        private $iNumber;

        public function __construct($iNumber)
        {
            $this->iNumber = $iNumber;
        }

        public function __toString()
        {
            return $this->iNumber;
        }
    }

}


