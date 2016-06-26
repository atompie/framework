<?php
namespace AtomPie\Core\Annotation\Tag {

    use AtomPie\Annotation\AnnotationTag;

    /**
     * @Annotation
     * @Target("METHOD")
     */
    class Log extends AnnotationTag
    {

        /**
         * @Enum ({"ExecutionTime", "Execution", "Parameters"})
         */
        public $What;

        /**
         * @return array
         */
        protected function getAllowedAttributes()
        {
            return array('What');
        }
    }

}
