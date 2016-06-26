<?php
namespace AtomPie\Service\Api {

    class RemoteException
    {

        private $sFile;
        private $iLine;
        private $sMessage;
        private $iCode;
        private $oException;
        private $aStackTrace;

        /**
         * RemoteException constructor.
         * @param $sFile
         * @param $iLine
         * @param $sMessage
         * @param array $aStackTrace
         * @param int $iCode
         * @param \Throwable $oException
         */
        public function __construct($sFile, $iLine, $sMessage, array $aStackTrace, $iCode = 0, $oException = null)
        {
            $this->sFile = $sFile;
            $this->iLine = $iLine;
            $this->sMessage = $sMessage;
            $this->iCode = $iCode;
            $this->aStackTrace = $aStackTrace;
            $this->oException = $oException;
        }

        /***
         * Gets the message
         * @link http://php.net/manual/en/throwable.getmessage.php
         * @return string
         * @since 7.0
         */
        public function getMessage()
        {
            return $this->sMessage;
        }

        /**
         * Gets the exception code
         * @link http://php.net/manual/en/throwable.getcode.php
         * @return int <p>
         * Returns the exception code as integer in
         * {@see Exception} but possibly as other type in
         * {@see Exception} descendants (for example as
         * string in {@see PDOException}).
         * </p>
         * @since 7.0
         */
        public function getCode()
        {
            return $this->iCode;
        }

        /**
         * Gets the file in which the exception occurred
         * @link http://php.net/manual/en/throwable.getfile.php
         * @return string Returns the name of the file from which the object was thrown.
         * @since 7.0
         */
        public function getFile()
        {
            return $this->sFile;
        }

        /**
         * Gets the line on which the object was instantiated
         * @link http://php.net/manual/en/throwable.getline.php
         * @return int Returns the line number where the thrown object was instantiated.
         * @since 7.0
         */
        public function getLine()
        {
            return $this->iLine;
        }

        /**
         * Gets the stack trace
         * @link http://php.net/manual/en/throwable.gettrace.php
         * @return array <p>
         * Returns the stack trace as an array in the same format as
         * {@see debug_backtrace()}.
         * </p>
         * @since 7.0
         */
        public function getTrace()
        {
            return $this->aStackTrace;
        }

        /**
         * Gets the stack trace as a string
         * @link http://php.net/manual/en/throwable.gettraceasstring.php
         * @return string Returns the stack trace as a string.
         * @since 7.0
         */
        public function getTraceAsString()
        {
            return implode(PHP_EOL, $this->aStackTrace);
        }

        /**
         * Returns the previous Throwable
         * @link http://php.net/manual/en/throwable.getprevious.php
         * @return \Exception Returns the previous {@see Throwable} if available, or <b>NULL</b> otherwise.
         * @since 7.0
         */
        public function getPrevious()
        {
            return $this->oException;
        }

        /**
         * Gets a string representation of the thrown object
         * @link http://php.net/manual/en/throwable.tostring.php
         * @return string <p>Returns the string representation of the thrown object.</p>
         * @since 7.0
         */
        public function __toString()
        {
            return
                'File: ' . $this->getFile() . PHP_EOL .
                'Line: ' . $this->getLine() . PHP_EOL .
                'Message: ' . $this->getMessage() . PHP_EOL .
                'StackTrace' . $this->getTraceAsString();

        }
    }

}
