<?php
namespace AtomPiePhpUnitTest\System;

use AtomPie\System\Application;
use AtomPie\System\Init;
use AtomPie\Core\FrameworkConfig;
use AtomPie\Core\Dispatch\DispatchManifest;
use AtomPie\System\Router;
use AtomPiePhpUnitTest\ApplicationConfigDefinition;
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
            $oEnv,
            new Router(__DIR__.'/../../AtomPieTestAssets/Routing/Routing.php'),
            new ApplicationConfigDefinition($oEnv->getEnv()),
            __DIR__,
            __DIR__);
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
