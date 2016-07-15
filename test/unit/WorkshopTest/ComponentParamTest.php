<?php
namespace WorkshopTest;

@session_start();

use AtomPie\Gui\Component\ComponentParam;
use AtomPie\Gui\Component\ComponentParamSessionKey;
use AtomPie\Gui\Component\Params;
use WorkshopTest\Resource\Component\MockComponent0;
use WorkshopTest\Resource\Config\Config;

class ComponentParamTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {

        $oConfig = Config::get();
        Boot::up($oConfig);
        parent::setUp();
    }

    /**
     * Cleans up the environment runAfter running a test.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testComponentParams_ComponentType()
    {
        $oComponentParam = new ComponentParam('Name', 'Value');
        $o = new Params($oComponentParam);
        $o->setComponentType(MockComponent0::Type());
        $aParams = $o->__toArray();
        $this->assertTrue(isset($aParams['Name']['MockComponent0']));
        $this->assertTrue($aParams['Name']['MockComponent0'] == 'Value');
    }

    public function testComponentParams_Component()
    {
        $oComponentParam = new ComponentParam('Name', 'Value');
        $o = new Params($oComponentParam);
        $o->setComponent(new MockComponent0('XXX'));
        $aParams = $o->__toArray();
        $this->assertTrue(isset($aParams['Name']['MockComponent0.XXX']));
        $this->assertTrue($aParams['Name']['MockComponent0.XXX'] == 'Value');
    }

    public function testComponentParams_Component_inParam()
    {

        $oContext = new ComponentParamSessionKey(new MockComponent0('XXX'));

        $oComponentParam = new ComponentParam('Name', 'Value');
        $oComponentParam->setComponentContext($oContext->__toString());

        $o = new Params($oComponentParam);
        $aParams = $o->__toArray();
        $this->assertTrue(isset($aParams['Name']['MockComponent0.XXX']));
        $this->assertTrue($aParams['Name']['MockComponent0.XXX'] == 'Value');
    }
}
