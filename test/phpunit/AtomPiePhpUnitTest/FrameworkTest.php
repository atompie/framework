<?php
namespace AtomPiePhpUnitTest {

    use AtomPie\Core\FrameworkConfig;
    use AtomPie\System\EndPointConfig;
    use AtomPie\System\Kernel;
    use AtomPie\System\Namespaces;
    use AtomPie\Web\Environment;

    class FrameworkTest extends \PHPUnit_Framework_TestCase
    {

        /**
         * @param $oConfig
         * @return \AtomPie\Web\Connection\Http\Response
         */
        protected function bootKernel($oConfig)
        {

            $oEnvironment = Environment::resetInstance();

            $oKernel = new Kernel($oEnvironment);
            return $oKernel->boot($oConfig);
        }

        /**
         * @param $sDefaultEndPoint
         * @param array $aContractFillers
         * @param array $aMiddleware
         * @param array $aContentProcessors
         * @return FrameworkConfig
         */
        protected function getDefaultConfig(
            $sDefaultEndPoint, 
            array $aContractFillers = [],
            array $aMiddleware = [], 
            array $aContentProcessors = [])
        {
            $oEnvironment = Environment::getInstance();

            $oConfig = new FrameworkConfig(
                $sDefaultEndPoint,
                new EndPointConfig(
                    new Namespaces(),
                    new Namespaces([
                        '\AtomPieTestAssets\Resource\Mock\MockEndPoint'
                    ])
                ),
                new ApplicationConfigSwitcher($oEnvironment->getEnv()),
                $oEnvironment,
                $aContractFillers,
                $aMiddleware,
                $aContentProcessors
            );
            return $oConfig;
        }
    }

}
