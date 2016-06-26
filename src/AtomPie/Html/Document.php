<?php
namespace AtomPie\Html {

    class Document extends Node
    {
        /**
         * @var \AtomPie\Html\ElementNode
         */
        public $documentElement;
        /**
         * @var string
         */
        public $doctype;

        public function __toString()
        {
            return $this->doctype . "\n" . $this->documentElement->__toString();
        }

        public function getElementsByTagName($sTagName)
        {
            return $this->documentElement->getElementsByTagName($sTagName);
        }
    }

}