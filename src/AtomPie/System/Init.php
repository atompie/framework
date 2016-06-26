<?php
namespace AtomPie\System {

    use AtomPie\Boundary\Core\Dispatch\IAmDispatchManifest;
    use AtomPie\Boundary\Core\IProcessContent;
    use AtomPie\Boundary\Core\ISetUpContentProcessor;
    use AtomPie\Boundary\Core\ISetUpDependencyContainer;
    use AtomPie\Boundary\Core\IAmFrameworkConfig;
    use AtomPie\DependencyInjection\Dependency;
    use AtomPie\System\DependencyContainer\EndPointDependencyContainer;
    use AtomPie\System\IO\File;
    use AtomPie\Web\Boundary\IAmEnvironment;

    class Init
    {

        /**
         * @var ContentProcessor
         */
        private $oContentProcessor;

        /**
         * @var \AtomPie\Core\Dispatch\DispatchManifest
         */
        private $oDispatchManifest;

        /**
         * @var IAmFrameworkConfig
         */
        private $oConfig;

        /**
         * @var ISetUpContentProcessor[]
         */
        private $aContentProcessors;

        public function __construct()
        {
        }

        /**
         * @param IAmEnvironment $oEnvironment
         * @param IAmFrameworkConfig $oConfig
         * @param IAmDispatchManifest $oDispatchManifest
         * @param array $aContentProcessors
         * @return Application
         */
        public function initApplication(
            IAmEnvironment $oEnvironment,
            IAmFrameworkConfig $oConfig,
            IAmDispatchManifest $oDispatchManifest,
            array $aContentProcessors
        ) {

            $this->oConfig = $oConfig;
            $this->oDispatchManifest = $oDispatchManifest;
            $this->oContentProcessor = new ContentProcessor();
            $this->aContentProcessors = $aContentProcessors;

            $oApplication = $this->newApplication(
                $oEnvironment,
                $this->oDispatchManifest,
                $this->oContentProcessor
            );

            // Merge dependencies for EndPoint -----------------

            // Add dependency injection for application

            $aDependencySet = [
                Application::class => function () use ($oApplication) {
                    return $oApplication;
                },
                IAmDispatchManifest::class => function () use ($oDispatchManifest) {
                    return $oDispatchManifest;
                },
            ];

            $oEndPointDependencyContainer = $this->factoryEndPointDependencyContainer(
                $oEnvironment,
                $aDependencySet
            );


            $oApplication->injectDependency($oEndPointDependencyContainer);

            // -----------------

            // Merge dependencies for content processors -----------------

            foreach ($this->aContentProcessors as $oContentProcessor) {
                if ($oContentProcessor instanceof ISetUpContentProcessor) {
                    $oContentProcessor->init($this->oDispatchManifest);
                    if ($oContentProcessor instanceof ISetUpDependencyContainer) {
                        $oContentProcessor->initDependencyContainer($aDependencySet);
                    }
                    $oContentProcessor->configureProcessor($this->oContentProcessor);
                }
            }

            // Sets dependency injections
            $this->oContentProcessor->processBefore();

            return $oApplication;

        }

        /**
         * @param \AtomPie\Web\Boundary\IAmEnvironment $oEnvironment
         * @param IAmDispatchManifest $oDispatchManifest
         * @param IProcessContent $oContentProcessor
         * @return Application
         */
        private function newApplication(
            IAmEnvironment $oEnvironment,
            IAmDispatchManifest $oDispatchManifest,
            IProcessContent $oContentProcessor
        ) {

            $oDispatcher = new Dispatcher($oDispatchManifest);

            return new Application(
                $oDispatcher,
                $oEnvironment,
                $oContentProcessor
            );

        }

        /**
         * @param IAmEnvironment $oEnvironment
         * @param $aDependencySet
         * @return EndPointDependencyContainer
         */
        private function factoryEndPointDependencyContainer(IAmEnvironment $oEnvironment, $aDependencySet)
        {

            $oDependency = new Dependency();
            $oDependency->addDependency($aDependencySet);

            $oThisEndPointUrlProvider = new UrlProvider(
                $this->oDispatchManifest,
                $oEnvironment->getRequest()->getAllParams()->getAll()
            );

            // EndPoint
            $oEndPointDependencyContainer = new EndPointDependencyContainer(
                $oEnvironment,
                $this->oConfig,
                $this->oDispatchManifest,
                $oThisEndPointUrlProvider);

            // All methods
            $oEndPointDependencyContainer
                ->forAnyClass()// EndPoints do not have defined class they extend so DI is for all types
                ->merge($oDependency);

            return $oEndPointDependencyContainer;
        }

    }

}
