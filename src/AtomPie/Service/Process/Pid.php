<?php
namespace AtomPie\Service\Process {

    class Pid
    {

        private $sFilename;
        private $bAlreadyRunning = false;

        function __construct($sDirectory)
        {

            $this->sFilename = $sDirectory . '/app.pid';

            if (is_writable($this->sFilename) || is_writable($sDirectory)) {

                if (file_exists($this->sFilename)) {
                    $pid = (int)trim(file_get_contents($this->sFilename));
                    if (posix_kill($pid, 0)) {
                        $this->bAlreadyRunning = true;
                    }
                }

            } else {
                die("Cannot write to pid file '$this->sFilename'. Program execution halted.\n");
            }

            if (!$this->bAlreadyRunning) {
                $pid = getmypid();
                file_put_contents($this->sFilename, $pid);
            }

        }

        public function isRunning()
        {
            return $this->bAlreadyRunning;
        }

        public function __destruct()
        {

            if (!$this->bAlreadyRunning && file_exists($this->sFilename) && is_writeable($this->sFilename)) {
                unlink($this->sFilename);
            }

        }

    }
}
