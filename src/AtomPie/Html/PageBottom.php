<?php
namespace AtomPie\Html {

    use Generi\Object;

    /**
     * @see \AtomPie\Html\PageBottom is singleton.
     * Handles all operations regarding template
     * header
     */
    final class PageBottom extends Object
    {

        /**
         * @var $this
         */
        private static $oInstance;
        /**
         * @var ScriptsCollection
         */
        private $oScriptCollection;

        private function __construct()
        {
            $this->oScriptCollection = new ScriptsCollection();
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

        public static function destroyInstance()
        {
            self::$oInstance = null;
        }

        /**
         * @return ScriptsCollection
         */
        public function getScriptCollection()
        {
            return $this->oScriptCollection;
        }

    }

}