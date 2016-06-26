<?php
namespace AtomPie\Gui\Component\Annotation\Tag {

    use AtomPie\Annotation\AnnotationTag;

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
