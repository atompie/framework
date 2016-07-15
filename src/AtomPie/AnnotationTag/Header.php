<?php
namespace AtomPie\AnnotationTag {

    /**
     * @Annotation
     * @Target("CLASS","METHOD")
     */
    final class Header extends AnnotationTag
    {

        /**
         * @var string
         */
        public $ContentType;

        /**
         * @var string
         */
        public $CacheControl;

        /**
         * @var string
         */
        public $Date;

        /**
         * @var string
         */
        public $Expires;

        /**
         * @var string
         */
        public $Server;

        /**
         * @var string
         */
        public $ContentEncoding;

        /**
         * @var string
         */
        public $ContentDisposition;

        /**
         * @return array
         */
        protected function getAllowedAttributes()
        {
            return array(
                'ContentType',
                'CacheControl',
                'Date',
                'Expires',
                'Server',
                'ContentEncoding',
                'ContentDisposition'
            );
        }

    }

}


