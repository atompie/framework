<?php
namespace AtomPiePhpUnitTest\System;

use AtomPie\System\Application;
use AtomPie\System\EndPointConfig;
use AtomPie\System\Init;
use AtomPie\Core\FrameworkConfig;
use AtomPie\Core\Dispatch\DispatchManifest;
use AtomPie\System\Namespaces;
use AtomPiePhpUnitTest\ApplicationConfigSwitcher;
use WorkshopTest\Boot;

class InitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReturnApplication()
    {
        $oEnv = Boot::getEnv();
        $oConfig = new FrameworkConfig(
            'Main',
            new EndPointConfig(new Namespaces()),
            new ApplicationConfigSwitcher($oEnv->getEnv()),
            $oEnv
        );
        $oDispatchManifest = DispatchManifest::factory(
            $oEnv->getRequest(),
            $oConfig,
            $oConfig->getDefaultEndPoint()
        );
        $oInit = new Init();
        $oApplication = $oInit->initApplication($oEnv, $oConfig, $oDispatchManifest, []);
        $this->assertTrue($oApplication instanceof Application);
    }
}
