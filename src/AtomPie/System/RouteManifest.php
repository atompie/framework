<?php
namespace AtomPie\System {

    use AtomPie\Boundary\System\IAddRoutes;

    class RouteManifest
    {

        /**
         * @var IAddRoutes
         */
        private $oRouter;
        private $aParams;
        private $aReverseMapping = [];

        public function __construct(IAddRoutes $oRouter)
        {
            $this->oRouter = $oRouter; //new \TreeRoute\Router();
        }

        /**
         * @param $sUrl
         * @return $this
         */
        public function url($sUrl)
        {
            $this->aParams = [];
            $this->aParams['url'] = $sUrl;
            $this->aParams['methods'] = ['GET'];
            $this->aParams['manifest'] = 'default';

            return $this;
        }

        /**
         * @param array $aMethods
         * @return $this
         */
        public function constrainTo(array $aMethods)
        {
            $this->aParams['methods'] = $aMethods;
            return $this;
        }

        public function routeTo($sDispatchManifest, array $aDependencies = [])
        {
            $this->aReverseMapping[$sDispatchManifest] = $this->aParams['url'];
            $this->oRouter->addRoute(
                $this->aParams['methods'],
                $this->aParams['url'],
                $sDispatchManifest
            );
        }

    }

}
