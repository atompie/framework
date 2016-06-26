<?php
namespace AtomPie\Core\Annotation\Tag {

    use AtomPie\Annotation\AnnotationTag;

    /**
     * @Annotation
     * @Target("METHOD","CLASS")
     */
    final class Documentation extends AnnotationTag
    {

        /**
         * @var string
         */
        public $Name;

        /**
         * @var String
         */
        public $File;

        /**
         * @return array
         */
        protected function getAllowedAttributes()
        {
            return array('Name', 'File');
        }
    }

}


