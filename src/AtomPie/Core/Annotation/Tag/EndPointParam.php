<?php
namespace AtomPie\Core\Annotation\Tag {

    use AtomPie\Annotation\AnnotationTag;

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


