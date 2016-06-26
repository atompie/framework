<?php
namespace AtomPie\System\IO {

    use AtomPie\System\Exception;

    class File
    {
        /**
         * @var string
         */
        public $sPath;

        public function __construct($sPath)
        {
            $this->sPath = $sPath;
        }

        /**
         * @return boolean
         */
        public function remove()
        {
            return unlink($this->sPath);
        }

        /**
         * @param string $sPath
         * @return boolean
         */
        public function rename($sPath)
        {
            return rename($this->sPath, $sPath);
        }

        /**
         * @return boolean
         */
        public function isValid()
        {
            return isset($this->sPath) && is_file($this->sPath);
        }

        /**
         * @return string
         */
        public function getPath()
        {
            return $this->sPath;
        }

        /**
         * Save string to file. Creates file if it does not exist.
         *
         * @param    string $sContent
         * @param    bool $bAppend
         * @throws File\Permission\Exception
         * @return    bool
         */
        public function save($sContent, $bAppend = false)
        {

            if (!$bAppend) {
                $resFileOpen = @fopen($this->sPath, 'wb');
            } else {
                $resFileOpen = @fopen($this->sPath, 'a');
            }

            if (false !== $resFileOpen) {
                fwrite($resFileOpen, $sContent);
                fflush($resFileOpen);
                fclose($resFileOpen);
                return true;
            } else {
                $aError = error_get_last();
                throw new File\Permission\Exception($aError['message']);
            }
        }

        /**
         * Loads content from file. Do not check if it is UTF-8 encoded.
         * Returns string on success or throws \System\Exception.
         *
         * @throws File\NotFoundException
         * @return    mixed
         */
        public function loadRaw()
        {
            // File must exist
            if ($this->isValid()) {
                $resFileHandler = @fopen($this->sPath, 'rb');
                $iFileSize = filesize($this->sPath);
                if ($iFileSize == '0') {
                    @fclose($resFileHandler);
                    return '';
                }
                $sContent = fread($resFileHandler, $iFileSize);
                fclose($resFileHandler);
                return $sContent;
            } else {
                throw new File\NotFoundException(sprintf('File %s does not exist!', $this->sPath));
            }
        }

        /**
         * Reads File. Returns read string or false or throws \System\Exception on Failure.
         *
         * @throws Exception
         * @return    mixed
         */
        public function load()
        {
            $sContent = $this->loadRaw();
            if (true !== mb_check_encoding($sContent, 'UTF-8')) {
                throw new Exception(sprintf('Content of file %s is not UTF-8 encoded!', $this->sPath));
            }
            return $sContent;
        }

        /**
         * @param bool $bCheckBrowserConnection
         * @return void
         */
        public function printOnScreen($bCheckBrowserConnection = true)
        {
            $rLocalFile = fopen($this->sPath, 'rb');
            if ($rLocalFile) {
                while (!feof($rLocalFile)) {
                    if ($bCheckBrowserConnection && connection_status() == 0) {
                        break;
                    }

                    print(fread($rLocalFile, 1024 * 8));
                    flush();
                }
                fclose($rLocalFile);
            }
        }

        /**
         * Check file checksum.
         *
         * @param    string    File name
         * @return    mixed    String or NULL
         */
        public function getChecksum()
        {
            if ($this->isValid()) {
                $sContent = $this->loadRaw();
                // Is OK, Never UTF-8 encoded
                return mb_strtoupper(dechex(crc32($sContent)));
            }
            return null;
        }

        public function getBasename()
        {
            return basename($this->sPath);
        }

        public function getExtension()
        {
            $aPathParts = pathinfo($this->sPath);
            return isset($aPathParts['extension']) ? strtolower($aPathParts['extension']) : null;
        }

        public function getName()
        {
            if (defined('PATHINFO_FILENAME')) {
                return pathinfo($this->sPath, PATHINFO_FILENAME);
            }
            if (strstr($this->sPath, '.')) {
                return substr($this->sPath, 0, strrpos($this->sPath, '.'));
            }
            return $this->sPath;
        }

        public function setName($sPath)
        {
            $this->sPath = $sPath;
        }

        public function getModificationTime()
        {
            clearstatcache();
            return @filemtime($this->sPath);
        }

        public function getCreationTime()
        {
            clearstatcache();
            return @filectime($this->sPath);
        }

        public function getAccessTime()
        {
            clearstatcache();
            return @fileatime($this->sPath);
        }

        public function getSize()
        {
            clearstatcache();
            return @filesize($this->sPath);
        }

        public function getOwner()
        {
            clearstatcache();
            return @fileowner($this->sPath);
        }

        public function getGroup()
        {
            clearstatcache();
            return @filegroup($this->sPath);
        }

        // TODO: Refactor move to permission class
        public function getUnixPermissions()
        {
            $iPerms = @fileperms($this->sPath);

            if (($iPerms & 0xC000) == 0xC000) {
                // Gniazdo (socket)
                $sInfo = 's';
            } elseif (($iPerms & 0xA000) == 0xA000) {
                // Link symboliczny
                $sInfo = 'l';
            } elseif (($iPerms & 0x8000) == 0x8000) {
                // Zwykły plik
                $sInfo = '-';
            } elseif (($iPerms & 0x6000) == 0x6000) {
                // Urządzenie blokowe
                $sInfo = 'b';
            } elseif (($iPerms & 0x4000) == 0x4000) {
                // Katalog
                $sInfo = 'd';
            } elseif (($iPerms & 0x2000) == 0x2000) {
                // Urządzenie znakowe
                $sInfo = 'c';
            } elseif (($iPerms & 0x1000) == 0x1000) {
                // Potok (FIFO)
                $sInfo = 'p';
            } else {
                // Nieznane
                $sInfo = 'u';
            }

            // Właściciel
            $sInfo .= (($iPerms & 0x0100) ? 'r' : '-');
            $sInfo .= (($iPerms & 0x0080) ? 'w' : '-');
            $sInfo .= (($iPerms & 0x0040) ?
                (($iPerms & 0x0800) ? 's' : 'x') :
                (($iPerms & 0x0800) ? 'S' : '-'));

            // Grupa
            $sInfo .= (($iPerms & 0x0020) ? 'r' : '-');
            $sInfo .= (($iPerms & 0x0010) ? 'w' : '-');
            $sInfo .= (($iPerms & 0x0008) ?
                (($iPerms & 0x0400) ? 's' : 'x') :
                (($iPerms & 0x0400) ? 'S' : '-'));

            // Świat
            $sInfo .= (($iPerms & 0x0004) ? 'r' : '-');
            $sInfo .= (($iPerms & 0x0002) ? 'w' : '-');
            $sInfo .= (($iPerms & 0x0001) ?
                (($iPerms & 0x0200) ? 't' : 'x') :
                (($iPerms & 0x0200) ? 'T' : '-'));

            return $sInfo;
        }
    }
}