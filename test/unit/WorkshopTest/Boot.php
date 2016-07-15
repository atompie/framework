<?php
namespace WorkshopTest {

    use AtomPie\Gui\Component\ComponentDependencyContainer;
    use AtomPie\System\EndPointConfig;
    use AtomPie\System\Namespaces;
    use AtomPie\System\UrlProvider;
    use AtomPie\System\Init;
    use AtomPie\Boundary\Core\IAmFrameworkConfig;
    use AtomPie\System\DependencyContainer\EndPointDependencyContainer;
    use AtomPie\Core\FrameworkConfig;
    use AtomPie\Core\Dispatch\DispatchManifest;
    use AtomPie\Web\Session\ParamStatePersister;
    use AtomPie\System\Kernel;
    use AtomPie\Web;
    use AtomPie\Web\Boundary\IAmEnvironment;
    use AtomPie\Web\Environment;
    use AtomPiePhpUnitTest\ApplicationConfigSwitcher;

    class Boot
    {

        /**
         * @return Environment
         */
        public static function getEnv()
        {
            return Environment::getInstance();
        }

        /**
         * @return \AtomPie\System\DependencyContainer\EndPointDependencyContainer
         */
        public static function getEndPointDi()
        {
            $oEnvironment = Boot::getEnv();
            $oConfig = self::getFrameworkConfig($oEnvironment);
            $oDispatchManifest = DispatchManifest::factory(
                $oEnvironment->getRequest(),
                $oConfig,
                $oConfig->getDefaultEndPoint()
            );

            $oThisUrlProvider = new UrlProvider(
                $oDispatchManifest,
                $oEnvironment->getRequest()->getAllParams()->getAll()
            );

            return new EndPointDependencyContainer($oEnvironment, $oConfig, $oDispatchManifest, $oThisUrlProvider);
        }

        public static function upRequest(IAmFrameworkConfig $oConfig, $oRequest)
        {

            $oEnvironment = Boot::getEnv();
            $oDispatchManifest = DispatchManifest::factory($oRequest,
                $oConfig,
                $oConfig->getDefaultEndPoint()
            );

            $oBoot = new Init();

            $oApplication = $oBoot->initApplication(
                $oEnvironment,
                $oConfig,
                $oDispatchManifest,
                $oConfig->getContentProcessors()
            );

            return $oApplication;

        }

        public static function up(
            IAmFrameworkConfig $oConfig,
            $sEndPoint = 'Default.index',
            $sEventSpecString = null,
            array $aParams = null
        ) {

            $oRequest = RequestFactory::produce(
                $sEndPoint,
                $sEventSpecString,
                $aParams
            );

            return self::upRequest($oConfig, $oRequest);
        }

        /**
         * @param IAmEnvironment $oEnvironment
         * @param IAmFrameworkConfig $oConfig
         * @param null $oLoader
         * @return Web\Boundary\IChangeResponse
         */
        public static function run(
            IAmEnvironment $oEnvironment,
            IAmFrameworkConfig $oConfig,
            $oLoader = null
        ) {

            $oKernel = new Kernel($oEnvironment, $oLoader);
            return $oKernel->boot($oConfig);
        }

        /**
         * @param null $oDispatchManifest
         * @return ComponentDependencyContainer
         */
        public static function getComponentDi($oDispatchManifest = null)
        {

            $oEnvironment = Boot::getEnv();
            if ($oDispatchManifest === null) {

                $oConfig = self::getFrameworkConfig($oEnvironment);
                $oDispatchManifest = DispatchManifest::factory(
                    $oEnvironment->getRequest(),
                    $oConfig,
                    $oConfig->getDefaultEndPoint()
                );

            }

            $oStatePersister = new ParamStatePersister(
                $oEnvironment->getSession(),
                $oDispatchManifest->getEndPoint()->__toString()
            );

            return new ComponentDependencyContainer(
                $oEnvironment,
                $oDispatchManifest,
                $oStatePersister
            );
        }

        /**
         * @param Environment $oEnvironment
         * @return FrameworkConfig
         */
        private static function getFrameworkConfig($oEnvironment)
        {
            return new FrameworkConfig(
                'Main',
                new EndPointConfig(new Namespaces()),
                new ApplicationConfigSwitcher($oEnvironment->getEnv()),
                $oEnvironment
            );
        }
    }

}
