<?php
namespace AtomPie\Core\Annotation\Tag {

    use AtomPie\Annotation\AnnotationTag;

    /**
     * @package Workshop\MetaData\Annotation
     * @Annotation
     * @Target("CLASS","METHOD")
     */
    final class Client extends AnnotationTag
    {

        /**
         * @var string
         */
        public $Accept;

        /**
         * @var string
         */
        public $Type;

        /**
         * @Enum({"GET","POST","PUT","DELETE","HEAD","OPTIONS"})
         */
        public $Method;

        /**
         * @var string
         */
        public $ContentType;

        /**
         * @return array
         */
        protected function getAllowedAttributes()
        {
            return array('Accept', 'ContentType', 'Type', 'Method');
        }

    }

}
