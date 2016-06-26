<?php
namespace AtomPie\Html {

    use Generi\Object;

    /**
     * @see \AtomPie\Html\HtmlHeader is singleton.
     * Handles all operations regarding template
     * header
     */
    final class HtmlHeader extends Object
    {

        /**
         * @var $this
         */
        private static $oInstance;
        /**
         * @var Tag\Head
         */
        private $oHead;

        private function __construct()
        {
            $this->oHead = new Tag\Head();
        }

        /**
         * @return $this
         */
        public static function getInstance()
        {
            if (is_null(self::$oInstance)) {
                self::$oInstance = new self();
            }

            return self::$oInstance;
        }

        /**
         * Destroys instance of @see \AtomPie\Html\HtmlHeader
         */
        public static function destroyInstance()
        {
            self::$oInstance = null;
        }

        /**
         * @return Tag\Head
         */
        public function getHeadTag()
        {
            return $this->oHead;
        }

    }
}