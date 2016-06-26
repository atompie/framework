<?php
namespace AtomPie\I18n {

    class Date
    {

        private $sDate;

        public function __construct($sDate)
        {
            $this->sDate = $sDate;
        }

        public function __toString()
        {
            return $this->sDate;
        }

    }

}