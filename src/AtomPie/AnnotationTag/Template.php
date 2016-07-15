<?php
namespace AtomPie\AnnotationTag {

    /**
     * @Annotation
     * @Target("CLASS")
     */
    final class Template extends AnnotationTag
    {

        /**
         * @var String
         */
        public $File;

        /**
         * @return array
         */
        protected function getAllowedAttributes()
        {
            return array('File');
        }
    }

}
