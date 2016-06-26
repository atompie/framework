<?php
namespace AtomPie\Core\Dispatch {

    class QueryString
    {

        const NAMESPACE_ESCAPE_CHAR = '-';
        
        /**
         * @param $sString
         * @return string
         */
        protected static function escape($sString)
        {
            return str_replace(self::NAMESPACE_ESCAPE_CHAR, '\\', $sString);
        }

        /**
         * @param $sString
         * @return string
         */
        public static function urlEscape($sString)
        {
            return str_replace('\\', self::NAMESPACE_ESCAPE_CHAR, $sString);
        }

    }
}