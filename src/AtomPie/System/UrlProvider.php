<?php
namespace AtomPie\System {

    use AtomPie\Boundary\Core\Dispatch\IAmDispatchManifest;
    use AtomPie\Boundary\Core\Dispatch\IProvideUrl;

    class UrlProvider implements IProvideUrl
    {

        /**
         * @var IAmDispatchManifest
         */
        private $oDispatchManifest;

        /**
         * @var array
         */
        private $aRequestParams;

        public function __construct(
            IAmDispatchManifest $oDispatchManifest,
            array $aRequestParams
        ) {

            $this->oDispatchManifest = $oDispatchManifest;
            $this->aRequestParams = $aRequestParams;
        }

        /**
         * Returns EndPointUrl.
         * This function is immutable and creates new EndPointEventUrl object.
         *
         * @return \AtomPie\Core\Dispatch\EndPointUrl
         */
        public function getUrl()
        {
            // TODO pass params
            return $this->oDispatchManifest->getEndPoint()->cloneEndPointUrl();
        }

    }

}
