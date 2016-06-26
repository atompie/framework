<?php
namespace AtomPie\Service\Monitoring {

    class Logger implements ICanLogMessages
    {

        /**
         * @var \AtomPie\Service\Monitoring\IAmProfileMessage[]
         */
        private $aLogs = array();
        /**
         * Micro time.
         *
         * @var float
         */
        private $iLoggerStartTime;

        /**
         * In order to profile times between logs set $iLoggerStartTime to
         * microtime(true).
         *
         * @param null $iLoggerStartTime
         */
        public function __construct($iLoggerStartTime = null)
        {
            $this->iLoggerStartTime = $iLoggerStartTime;
        }

        public function log(IAmProfileMessage $oLog, $bDeferredPersistence = false)
        {

            if ($this->iLoggerStartTime !== null) {
                $oLog->measureTimeFrom($this->iLoggerStartTime);
            }

            if ($bDeferredPersistence === true) {
                $this->aLogs[] = $oLog;
                return true;
            } else {
                return $oLog->log();
            }

        }

        public function __destruct()
        {
            if (!empty($this->aLogs)) {
                foreach ($this->aLogs as $oLog) {
                    $oLog->log();
                }
            }
        }

    }

}
