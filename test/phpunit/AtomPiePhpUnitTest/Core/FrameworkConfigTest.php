<?php
namespace AtomPiePhpUnitTest\Core;

use AtomPie\Core\FrameworkConfig;
use AtomPie\System\EndPointConfig;
use AtomPie\System\Namespaces;
use AtomPie\Web\Environment;
use AtomPiePhpUnitTest\ApplicationConfig;
use AtomPiePhpUnitTest\ApplicationConfigSwitcher;

class FrameworkConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldSetAppConfiguration()
    {
        $oEnvironment = Environment::getInstance();
        
        $oConfig = new FrameworkConfig(
            'Main'
            , new EndPointConfig(new Namespaces())
            , new ApplicationConfigSwitcher($oEnvironment->getEnv())
            , $oEnvironment
        );

        $oAppConfig = $oConfig->getAppConfig();
        $this->assertTrue($oAppConfig instanceof ApplicationConfig);
    }
}
