<?php
namespace AtomPie\System\Output {

    /**
     * Buffers output.
     *
     */
    class Buffer
    {

        /**
         * Opens buffer.
         *
         * @param string $sCallBack
         * @return bool
         */
        public static function start($sCallBack = null)
        {
            if (is_null($sCallBack)) {
                return ob_start();
            } else {
                return ob_start($sCallBack);
            }
        }

        /**
         * Close buffer.
         *
         * @return bool
         */
        public static function end()
        {
            return ob_end_clean();
        }

        /**
         * Flush buffer content.
         *
         * @return bool
         */
        public static function flush()
        {
            return ob_end_flush();
        }

        /**
         * Returns buffer content.
         *
         * @return string
         */
        public static function get()
        {
            return ob_get_contents();
        }

    }

}