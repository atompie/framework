<?php
namespace AtomPie\Web\Connection\Http {

    // TODO: Refactor to use Response

    use AtomPie\System\IO\File;
    use AtomPie\Web\Exception;

    class Download
    {
        /**
         * @var File
         */
        private $oLocalFile;
        private $sContentType;
        private $bForceSafeAs;

        public function __construct(File $oLocalFile, $sContentType, $bForceSafeAs = true)
        {
            $this->oLocalFile = $oLocalFile;
            $this->sContentType = $sContentType;
            $this->bForceSafeAs = $bForceSafeAs;
        }

        public function streamXSendFile(File $oRemoteFile)
        {

            if (!$this->oLocalFile->isValid()) {
                throw new Exception('File "' . $this->oLocalFile->getName() . '" not available! System could not locate file on the local storage!');
            }

            header("X-Sendfile: " . $this->oLocalFile->getName());
            header("Content-type: " . $this->sContentType);

            if ($this->bForceSafeAs) {
                header('Content-Disposition: attachment; filename="' . $oRemoteFile->getName() . '"');
            }

            header("Content-Length: " . $this->oLocalFile->getSize());

        }

        public function streamFile(File $oRemoteFile)
        {

            if (!$this->oLocalFile->isValid()) {
                throw new Exception('File "' . $this->oLocalFile->getName() . '" is missing!');
            }

            set_time_limit(0);

            if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match("/MSIE/", $_SERVER['HTTP_USER_AGENT'])) {
                // IE Bug in download name workaround
                ini_set('zlib.output_compression', 'Off');
            }
            header('Content-type: ' . $this->sContentType);
            if ($this->bForceSafeAs) {
                header('Content-Disposition: attachment; filename="' . $oRemoteFile->getBasename() . '"');
            }
            header('Expires: ' . gmdate("D, d M Y H:i:s",
                    mktime(date("H") + 2, date("i"), date("s"), date("m"), date("d"), date("Y"))) . ' GMT');
            header('Accept-Ranges: bytes');
            header('Cache-control: no-cache, must-revalidate');
            header('Pragma: private');

            $iSize = $this->oLocalFile->getSize();
            if (isset($_SERVER['HTTP_RANGE'])) {
                /** @noinspection PhpUnusedLocalVariableInspection */
                list($a, $sRange) = explode("=", $_SERVER['HTTP_RANGE']);
                //if yes, download missing part
                str_replace($sRange, "-", $sRange);
                $iSize2 = $iSize - 1;
                $iNewLength = $iSize2 - $sRange;
                header("HTTP/1.1 206 Partial Content");
                header("Content-Length: $iNewLength");
                header("Content-Range: bytes $sRange$iSize2/$iSize");
            } else {
                $iSize2 = $iSize - 1;
                header("Content-Range: bytes 0-$iSize2/$iSize");
                header("Content-Length: " . $iSize);
            }

            $this->oLocalFile->printOnScreen();
        }
    }
}