<?php
namespace AtomPie\AnnotationTag {

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
