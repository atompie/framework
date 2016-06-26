<?php
namespace AtomPie\System {

    use AtomPie\Boundary\Core\IAmFrameworkConfig;
    use AtomPie\Core\Dispatch\DispatchManifest;
    use AtomPie\DependencyInjection\DependencyContainer;
    use AtomPie\System\IO\File;
    use AtomPie\Web\Boundary\IAmEnvironment;
    use AtomPie\Web\Connection\Http\Header;
    use AtomPie\Web\Connection\Http\Request;

    /**
     * Responsible for:
     *  - init of app
     */
    class Bootstrap
    {

        /**
         * @var IAmEnvironment
         */
        private $oEnvironment;

        /**
         * @var IAmFrameworkConfig
         */
        private $oConfig;

        public function __construct(
            IAmFrameworkConfig $oConfig,
            IAmEnvironment $oEnvironment
        ) {
            $this->oEnvironment = $oEnvironment;
            $this->oConfig = $oConfig;
        }

        /**
         * Init application. During application init Exception may be thrown.
         *
         * Pass custom dependency injection container.
         * Standard dependency containers are already defined.
         *
         *
         * @param DispatchManifest $oDispatchManifest
         * @param DependencyContainer $oCustomDependencyContainer
         * @param \AtomPie\Boundary\Core\ISetUpContentProcessor[] $aContentProcessors
         * @return Application
         * @throws \AtomPie\EventBus\Exception
         */
        public function initApplication(
            DispatchManifest $oDispatchManifest,
            DependencyContainer $oCustomDependencyContainer = null,
            array $aContentProcessors = []
        ) {

            $oSystemInit = new Init();

            ///////////////////////////////////
            // Sets dependency injections, etc

            $oApplication = $oSystemInit->initApplication(
                $this->oEnvironment,
                $this->oConfig,
                $oDispatchManifest,
                $aContentProcessors);

            if (isset($oCustomDependencyContainer)) {
                $oApplication
                    ->getEndPointDependencyContainer()
                    ->merge($oCustomDependencyContainer);
            }

            return $oApplication;
        }

    }

}
