<?php
namespace WorkshopTest;

@session_start();

require_once __DIR__ . '/../Config.php';

use AtomPie\Core\FrameworkConfig;
use AtomPie\Core\Dispatch\DispatchManifest;
use AtomPie\Core\Dispatch\EndPointImmutable;
use AtomPie\System\EndPointConfig;
use AtomPie\System\Namespaces;
use AtomPie\Web\Environment;
use AtomPie\Web\Session\ParamStatePersister;
use AtomPie\Web\Connection\Http\Url\Param;
use AtomPiePhpUnitTest\ApplicationConfigSwitcher;
use WorkshopTest\Resource\Config\Config;

class StatePersisterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $_REQUEST = array();
        $oConfig = Config::get();
        Boot::up($oConfig);
        parent::setUp();
    }

    /**
     * Cleans up the environment runAfter running a test.
     */
    protected function tearDown()
    {
        Environment::destroyInstance();
        $_REQUEST = array();
        parent::tearDown();
    }

    public function testAddState()
    {
        $aArray['a'] = array(1, 2, 3);

        $oParam = new Param('key', $aArray);

        $oConfig = $this->getConfig();

        $oDispatchManifest = new DispatchManifest($oConfig, new EndPointImmutable('Default.index'));

        $oStatePersister = new ParamStatePersister(Boot::getEnv()->getSession(),
            $oDispatchManifest->getEndPoint()->__toString());

        $oStatePersister->saveState($oParam, ParamStatePersister::VALUE);

        $this->assertTrue(isset($_SESSION['@IPersistValue/Default.index/key']['global-context']['a']));
        $this->assertTrue($_SESSION['@IPersistValue/Default.index/key']['global-context']['a'][0] == 1);

        $oParam1 = new Param('key', 1);
        $oParam2 = new Param('key', 2);

        $oStatePersister->saveState($oParam1, ParamStatePersister::VALUE, 'context1');
        $oStatePersister->saveState($oParam2, ParamStatePersister::VALUE, 'context2');

        $this->assertTrue(isset($_SESSION['@IPersistValue/Default.index/key']['context1']));
        $this->assertTrue(isset($_SESSION['@IPersistValue/Default.index/key']['context2']));

        $this->assertTrue($_SESSION['@IPersistValue/Default.index/key']['context1'] == 1);
        $this->assertTrue($_SESSION['@IPersistValue/Default.index/key']['context2'] == 2);

    }

    public function testRemoveState()
    {

        $oParam1 = new Param('key', 1);
        $oParam2 = new Param('key', 2);

        $oConfig = $this->getConfig();
        $oDispatchManifest = new DispatchManifest($oConfig, new EndPointImmutable('Default.index'));

        $oStatePersister = new ParamStatePersister(Boot::getEnv()->getSession(),
            $oDispatchManifest->getEndPoint()->__toString());

        $oStatePersister->saveState($oParam1, ParamStatePersister::VALUE, 'context1');
        $oStatePersister->saveState($oParam2, ParamStatePersister::VALUE, 'context2');

        $oStatePersister->removeState($oParam1->getName(), false, 'context1');

        $this->assertFalse(isset($_SESSION['@IPersistValue/Default.index/key']['context1']));
        $this->assertTrue(isset($_SESSION['@IPersistValue/Default.index/key']['context2']));

    }

    public function testRemoveState_RemoveAllValuesRegardlessContext()
    {

        $oParam1 = new Param('key', 1);
        $oParam2 = new Param('key', 2);

        $oConfig = $this->getConfig();
        $oDispatchManifest = new DispatchManifest($oConfig, new EndPointImmutable('Default.index'));

        $oStatePersister = new ParamStatePersister(Boot::getEnv()->getSession(),
            $oDispatchManifest->getEndPoint()->__toString());

        $oStatePersister->saveState($oParam1, ParamStatePersister::VALUE, 'context1');
        $oStatePersister->saveState($oParam2, ParamStatePersister::VALUE, 'context2');

        $oStatePersister->removeState($oParam1->getName(), true, 'context1');
        $this->assertFalse(isset($_SESSION['@IPersistValue/Default.index/key']['context1']));
        $this->assertFalse(isset($_SESSION['@IPersistValue/Default.index/key']['context2']));

    }

    public function testRemoveState_GlobalContext()
    {

        $oParam1 = new Param('key', 1);
        $oParam2 = new Param('key', 2);

        $oConfig = $this->getConfig();
        $oDispatchManifest = new DispatchManifest($oConfig, new EndPointImmutable('Default.index'));

        $oStatePersister = new ParamStatePersister(Boot::getEnv()->getSession(),
            $oDispatchManifest->getEndPoint()->__toString());

        $oStatePersister->saveState($oParam1, ParamStatePersister::VALUE);
        $oStatePersister->saveState($oParam2, ParamStatePersister::VALUE);

        $oStatePersister->removeState($oParam1->getName());
        $this->assertFalse(isset($_SESSION['@IPersistValue/Default.index/key']));

    }

    public function testLoadState()
    {

        $oParam1 = new Param('key', 1);
        $oParam2 = new Param('key', 2);

        $oConfig = $this->getConfig();
        $oDispatchManifest = new DispatchManifest($oConfig, new EndPointImmutable('Default.index'));

        $oStatePersister = new ParamStatePersister(Boot::getEnv()->getSession(),
            $oDispatchManifest->getEndPoint()->__toString());

        $oStatePersister->saveState($oParam1, ParamStatePersister::VALUE, 'context1');
        $oStatePersister->saveState($oParam2, ParamStatePersister::VALUE, 'context2');

        $oParam3 = new Param('key', $oStatePersister->loadState('key', 'context2'));

        $this->assertTrue($oParam3->getValue() == 2);
    }

    public function testLoadState_GlobalContext()
    {

        $oParam1 = new Param('key', 1);
        $oParam2 = new Param('key', 3);

        $oConfig = $this->getConfig();
        $oDispatchManifest = new DispatchManifest($oConfig, new EndPointImmutable('Default.index'));

        $oStatePersister = new ParamStatePersister(Boot::getEnv()->getSession(),
            $oDispatchManifest->getEndPoint()->__toString());

        $oStatePersister->saveState($oParam1, ParamStatePersister::VALUE);
        $oStatePersister->saveState($oParam2, ParamStatePersister::VALUE);

        $oParam3 = new Param('key', $oStatePersister->loadState('key'));

        $this->assertTrue($oParam3->getValue() == 3);
    }

    /**
     * @return FrameworkConfig
     */
    private function getConfig()
    {
        $oEnvironment = Environment::getInstance();
        return new FrameworkConfig(
            'Main',
            new EndPointConfig(new Namespaces()),
            new ApplicationConfigSwitcher($oEnvironment->getEnv()),
            $oEnvironment
        );
    }
}
