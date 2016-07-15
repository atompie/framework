<?php
namespace AtomPie\AnnotationTag {

    /**
     * @Annotation
     * @Target("METHOD")
     */
    final class EndPointParam extends AnnotationTag
    {

        /**
         * @var string
         */
        public $Name;

        /**
         * @var String
         */
        public $Description;

        /**
         * @return array
         */
        protected function getAllowedAttributes()
        {
            return array('Name', 'Description');
        }
    }

}


