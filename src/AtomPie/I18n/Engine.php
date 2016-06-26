<?php
namespace AtomPie\I18n {

    class Engine
    {

        const GET_TEXT = 1;

        private $iEngine;

        public function __construct($iEngine)
        {
            if ($iEngine != self::GET_TEXT) {
                throw new Exception('Wrong engine type.');
            }

            $this->iEngine = $iEngine;
        }

        public function isGetTextBased()
        {
            return $this->iEngine == self::GET_TEXT;
        }

    }

}
 