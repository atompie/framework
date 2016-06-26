<?php
namespace AtomPiePhpUnitTest\Core;

use AtomPie\Core\FrameworkConfig;
use AtomPie\System\Router;
use AtomPie\Web\Environment;
use AtomPiePhpUnitTest\ApplicationConfig;
use AtomPiePhpUnitTest\ApplicationConfigDefinition;

class FrameworkConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldSetAppConfiguration()
    {
        $oEnvironment = Environment::getInstance();
        
        $oConfig = new FrameworkConfig(
            $oEnvironment,
            new Router(__DIR__.'/../../AtomPieTestAssets/Routing/Routing.php'),
            new ApplicationConfigDefinition($oEnvironment->getEnv()),
            __DIR__,
            __DIR__,
            [],
            [],
            [],
            [],
            'Main'
        );

        $oAppConfig = $oConfig->getAppConfig();
        $this->assertTrue($oAppConfig instanceof ApplicationConfig);
    }
}
