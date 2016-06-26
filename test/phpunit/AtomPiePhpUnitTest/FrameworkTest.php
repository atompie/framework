<?php
namespace AtomPiePhpUnitTest {

    use AtomPie\Core\FrameworkConfig;
    use AtomPie\System\Kernel;
    use AtomPie\System\Router;
    use AtomPie\Web\Environment;

    class FrameworkTest extends \PHPUnit_Framework_TestCase
    {

        /**
         * @param $oConfig
         * @param array $aContentProcessors
         * @param array $aMiddleware
         * @return \AtomPie\Web\Connection\Http\Response
         */
        protected function bootKernel($oConfig, array $aContentProcessors, array $aMiddleware)
        {

            $oEnvironment = Environment::resetInstance();

            $oKernel = new Kernel($oConfig, $oEnvironment, $aMiddleware);
            return $oKernel->boot(
                $aContentProcessors
            );
        }

        /**
         * @param $sDefaultEndPoint
         * @return \AtomPie\Core\FrameworkConfig
         */
        protected function getDefaultConfig($sDefaultEndPoint)
        {
            $oEnvironment = Environment::getInstance();
            
            $oConfig = new FrameworkConfig(
                $oEnvironment, 
                new Router(__DIR__.'/../AtomPieTestAssets/Routing/Routing.php'),
                new ApplicationConfigDefinition($oEnvironment->getEnv()),
                __DIR__,
                __DIR__ . '/../AtomPieTestAssets/Resource/Theme',
                [],
                [],
                [
                    '\AtomPieTestAssets\Resource\Mock\MockEndPoint'
                ],
                [],
                $sDefaultEndPoint
            );
            return $oConfig;
        }
    }

}
