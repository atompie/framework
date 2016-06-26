<?php
namespace AtomPie\Core\Annotation\Tag {

    use AtomPie\Annotation\AnnotationTag;

    /**
     * @Annotation
     * @Target("METHOD","CLASS")
     */
    final class EndPoint extends AnnotationTag
    {

        /**
         * @var String
         */
        public $ContentType;

        /**
         * @return array
         */
        protected function getAllowedAttributes()
        {
            return array('ContentType');
        }
    }

}


