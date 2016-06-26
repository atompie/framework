<?php
namespace WorkshopTest;

use AtomPie\System\Application;
use AtomPie\Web\Environment;
use AtomPie\Gui\Component\ComponentParam;
use AtomPie\Html\Tag\Head;
use AtomPie\Web\Connection\Http\Url\Param;
use WorkshopTest\Resource\Component\MockComponent0;
use WorkshopTest\Resource\Component\MockComponent7;
use WorkshopTest\Resource\Config\Config;

class ComponentTest extends \PHPUnit_Framework_TestCase
{

    public function testEcho()
    {
        $sEndPoint = MockComponent7::Type()->getName() . '.EndPoint';
        $oRequest = RequestFactory::produce(
            $sEndPoint,
            null,
            array(
                'Param' => 'Param',
                'ComponentParam' => array('MockComponent7' => 'MockComponent7')
            ));

        $oConfig = Config::get();
        $oApplication = Boot::upRequest($oConfig, $oRequest);

        $this->assertTrue(false !== strstr($oApplication->run($oConfig)->__toString(), 'MockComponent7'));
    }

    public function testComponentFactory_InsideConstructor()
    {
        $oComponent = new MockComponent0();
        // Component is factored outside the __constructor so
        // IsFactored should not be true
        $this->assertTrue(!isset($oComponent->IsFactoryInvoked));
    }

    public function testComponentFactory_FullProcess_AllDiParams()
    {
        $sEndPoint = MockComponent7::Type()->getName() . '.EndPoint';
        $oRequest = RequestFactory::produce(
            $sEndPoint,
            null,
            array(
                'Param' => 'Param',
                'ComponentParam' => array('MockComponent7' => 'MockComponent7')
            ));

        $oConfig = Config::get();
        $oApplication = Boot::upRequest($oConfig, $oRequest);

        $oPhpUnit = $this;
        /** @noinspection PhpUnusedParameterInspection */
        $oApplication->handleEvent(Application::EVENT_BEFORE_RENDERING,
            function ($oSender, MockComponent7 $oContent) use ($oPhpUnit) {
                $this->assertTrue($oContent->Environment instanceof Environment);
                $this->assertTrue($oContent->ComponentParam instanceof ComponentParam);
                $this->assertTrue($oContent->Head instanceof Head);
                $this->assertTrue($oContent->Param instanceof Param);
            }
        );
        $oConfig = Config::get();
        $oApplication->run($oConfig);
    }

    public function testComponentFactory_FullProcess()
    {
        $sEndPoint = MockComponent0::Type()->getName() . '.EndPoint';
        $oConfig = Config::get();
        $oApplication = $this->getApp($sEndPoint, $oConfig);
        $oPhpUnit = $this;
        /** @noinspection PhpUnusedParameterInspection */
        $oApplication->handleEvent(Application::EVENT_BEFORE_RENDERING,
            function ($oSender, MockComponent0 $oContent) use ($oPhpUnit) {
                $oPhpUnit->assertTrue($oContent->IsFactoryInvoked === true);
                $oPhpUnit->assertTrue($oContent->oEnv === Boot::getEnv());
            });
        $oConfig = Config::get();
        $oApplication->run($oConfig);
    }

    public function testComponentProcess_FullProcess()
    {
        $sEndPoint = MockComponent0::Type()->getName() . '.EndPoint';

        $oConfig = Config::get();
        $oApplication = $this->getApp($sEndPoint, $oConfig);

        $oPhpUnit = $this;
        /** @noinspection PhpUnusedParameterInspection */
        $oApplication->handleEvent(Application::EVENT_BEFORE_RENDERING,
            function ($oSender, MockComponent0 $oContent) use ($oPhpUnit) {
                $oPhpUnit->assertTrue($oContent->IsProcessInvoked === true);
                $oPhpUnit->assertTrue($oContent->oHead instanceof Head);
            }
        );
        $oConfig = Config::get();
        $oApplication->run($oConfig);
    }

    /**
     * @param $sEndPoint
     * @param $oConfig
     * @return Application
     */
    private function getApp($sEndPoint, $oConfig)
    {
        return Boot::up($oConfig, $sEndPoint);
    }
}
