<?php
namespace AtomPie\MiddleWare {

    use AtomPie\Boundary\Core\IAmFrameworkConfig;
    use AtomPie\Boundary\System\IRunBeforeMiddleware;
    use AtomPie\System\Namespaces;
    use AtomPie\Web\Boundary\IChangeRequest;
    use AtomPie\Web\Boundary\IChangeResponse;
    use AtomPie\Web\Connection\Http\Header\Accept;
    use AtomPie\Web\Connection\Http\ImmutableHeader;

    class ApiVersioning implements IRunBeforeMiddleware {

        /**
         * @var string
         */
        private $sVersionedApiNamespace;

        /**
         * @var IAmFrameworkConfig
         */
        private $oEndPointNamespaces;

        /**
         * @var string
         */
        private $sVersionHeader;

        /**
         * ApiVersioning constructor.
         * @param $sVersionedApiNamespace
         * @param Namespaces $oEndPointNamespaces
         * @param string $sVersionHeader
         */
        public function __construct($sVersionedApiNamespace, Namespaces $oEndPointNamespaces, $sVersionHeader = 'application/vnd.atompie+json') {
            $this->sVersionedApiNamespace = $sVersionedApiNamespace;
            $this->oEndPointNamespaces = $oEndPointNamespaces;
            $this->sVersionHeader = $sVersionHeader;
        }

        /**
         * Returns modified Request.
         *
         * @param IChangeRequest $oRequest
         * @param IChangeResponse $oResponse
         * @return IChangeRequest
         * @throws Exception
         */
        public function before(IChangeRequest $oRequest, IChangeResponse $oResponse) {
            if($oRequest->hasHeader(ImmutableHeader::ACCEPT)) {
                /** @var Accept $oAccept */
                $oAccept = $oRequest->getHeader(ImmutableHeader::ACCEPT);

                if($oAccept->willYouAcceptMediaType($this->sVersionHeader, true)) {
                    $oMediaType = $oAccept->getMediaType($this->sVersionHeader);
                    if(isset($oMediaType->params['version'])) {

                        $sCurrentVersionNamespace = $this->getVersionNamespace($oMediaType->params['version']);

                        if(!$this->oEndPointNamespaces->hasEndPointNamespace($sCurrentVersionNamespace)) {
                            $this->oEndPointNamespaces->prependEndPointNamespace($sCurrentVersionNamespace);
                        }

                    }
                }
            }
            return $oRequest;
        }

        /**
         * @param $sVersion
         * @return string
         */
        private function getVersionNamespace($sVersion)
        {
            return
                $this->sVersionedApiNamespace .
                '\\v' . str_replace('.', '_', $sVersion);
        }

    }

}
