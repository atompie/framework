<?php
namespace AtomPie\Annotation {

    class AnnotationLine
    {
        
        public function __construct($sAnnotationClassName)
        {
            $this->ClassName = $sAnnotationClassName;
            $this->Attributes = new Attributes();
        }

        /**
         * @var string
         */
        public $ClassName;

        /**
         * @var Attributes
         */
        public $Attributes;
    }
}