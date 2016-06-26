<?php
namespace AtomPie\Web {

    use AtomPie\Web\Boundary\IAmServer;

    class Server implements IAmServer
    {

        const LOCALHOST_IP = '127.0.0.1';
        const LOCALHOST = 'localhost';

        /**
         * @var
         */
        private $aServer;

        /**
         * @var $this
         */
        private static $oInstance;

        /**
         * @param bool $bRecreate
         * @return Server
         */
        public static function getInstance($bRecreate = false)
        {
            if (!isset(self::$oInstance) || $bRecreate === true) {
                self::$oInstance = new self();
            }

            return self::$oInstance;
        }

        private function __construct()
        {
            $this->aServer = $_SERVER;
        }

        public function getServerUri()
        {
            return $this->getServer() . $this->getRequestUri();
        }

        public function getServerUrl($sUrl = null, $bWithPort = true)
        {
            if (is_null($sUrl)) {
                return $this->getServer($bWithPort) . $this->getSelfPhpFile();
            }
            return $this->getServer() . $sUrl;
        }

        public function getServer($bWithPort = true)
        {
            if (!$this->hasServerName()) {
                return null;
            }

            $sHttp = 'http://';
            if ($this->isHttps()) {
                $sHttp = 'https://';
            }

            if ($bWithPort) {
                return $sHttp . $this->getServerName() . ':' . $this->getPort();
            }
            return $sHttp . $this->getServerName();
        }

        public function getServerName()
        {
            if (isset($this->aServer['SERVER_NAME'])) {
                return $this->aServer['SERVER_NAME'];
            }
            return null;
        }

        public function getServerPhpFolder()
        {
            $sFolder = Server::getSelfPhpFolder();
            return Server::getServer() . $sFolder;
        }

        /**
         * @return string | null
         */
        public function getSelfPhpFile()
        {
            if (isset($this->aServer['PHP_SELF'])) {
                return $this->aServer['PHP_SELF'];
            } else {
                if (isset($this->aServer['SCRIPT_NAME'])) {
                    return $this->aServer['SCRIPT_NAME'];
                }
            }
            return null;
        }

        /**
         * @return string
         */
        public function getSelfPhpFolder()
        {
            $aExploded = explode('/', $this->getSelfPhpFile());
            array_pop($aExploded);
            $sFolder = implode('/', $aExploded);
            if (empty($sFolder)) {
                return '/';
            }
            return $sFolder . '/';
        }

        public function getRequestUri()
        {
            return (isset($this->aServer['REQUEST_URI']))
                ? $this->aServer['REQUEST_URI']
                : null;
        }

        public function getDocumentRoot()
        {
            return (isset($this->aServer['DOCUMENT_ROOT']))
                ? $this->aServer['DOCUMENT_ROOT']
                : null;
        }

        public function getContextDocumentRoot()
        {
            return (isset($this->aServer['CONTEXT_DOCUMENT_ROOT']))
                ? $this->aServer['CONTEXT_DOCUMENT_ROOT']
                : null;
        }

        public function getScriptName()
        {
            return (isset($this->aServer['SCRIPT_NAME']))
                ? $this->aServer['SCRIPT_NAME']
                : null;
        }

        public function getScriptFileName()
        {
            return (isset($this->aServer['SCRIPT_FILENAME']))
                ? $this->aServer['SCRIPT_FILENAME']
                : null;
        }

        public function getServerAdmin()
        {
            return (isset($this->aServer['SERVER_ADMIN']))
                ? $this->aServer['SERVER_ADMIN']
                : null;
        }

        public function getRequestScheme()
        {
            return (isset($this->aServer['REQUEST_SCHEME']))
                ? $this->aServer['REQUEST_SCHEME']
                : null;
        }

        public function getProtocol()
        {
            return (isset($this->aServer['SERVER_PROTOCOL']))
                ? $this->aServer['SERVER_PROTOCOL']
                : null;
        }

        public function getRemotePort()
        {
            return (isset($this->aServer['REMOTE_PORT']))
                ? $this->aServer['REMOTE_PORT']
                : null;
        }

        public function getRequestMethod()
        {
            return (isset($this->aServer['REQUEST_METHOD']))
                ? $this->aServer['REQUEST_METHOD']
                : null;
        }

        public function getQueryString()
        {
            return (isset($this->aServer['QUERY_STRING']))
                ? $this->aServer['QUERY_STRING']
                : null;
        }

        public function getIp()
        {

            if (isset($this->aServer['REMOTE_ADDR'])) {
                return $this->aServer['REMOTE_ADDR'];
            } elseif (isset($this->aServer['HTTP_X_FORWARDED_FOR'])) {
                return $this->aServer['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($this->aServer['HTTP_CLIENT_IP'])) {
                return $this->aServer['HTTP_CLIENT_IP'];
            }

            return null;

        }

        public function isLocalHost()
        {
            $sIp = $this->getIp();
            return $sIp == self::LOCALHOST_IP || $sIp == self::LOCALHOST;
        }

        /**
         * @return bool
         */
        public function isHttps()
        {
            return isset($this->aServer["HTTPS"]) && $this->aServer["HTTPS"] == "on";
        }

        /**
         * @return mixed
         */
        public function getHost()
        {
            return $this->aServer["HTTP_HOST"];
        }

        /**
         * @return mixed
         */
        public function getPort()
        {
            return $this->aServer['SERVER_PORT'];
        }

        /**
         * @return bool
         */
        private function hasServerName()
        {
            return isset($this->aServer['SERVER_NAME']) && isset($this->aServer['SERVER_PORT']);
        }
    }
}
