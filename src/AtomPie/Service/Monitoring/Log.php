<?php
namespace AtomPie\Service\Monitoring {

    class Log implements IAmProfileMessage
    {

        private $sMessage;
        private $iMessageType;
        private $sDestination;
        private $iLogTime;
        private $iStartTime;

        public function __construct($sMessage, $iMessageType = 0, $sDestination = null)
        {
            $this->sMessage = $sMessage;
            $this->iMessageType = $iMessageType;
            $this->sDestination = $sDestination;
            $this->iLogTime = microtime(true);
        }

        public function log()
        {
            return error_log(
                $this->toMessage(),
                $this->iMessageType,
                $this->sDestination
            );
        }

        public function measureTimeFrom($iStartTime)
        {
            $this->iStartTime = $iStartTime;
        }

        private function toMessage()
        {
            if ($this->iStartTime === null) {
                return sprintf('%s',
                    $this->sMessage
                );
            }

            return sprintf('[time %f] %s',
                $this->iLogTime - $this->iStartTime,
                $this->sMessage
            );
        }

    }

}
