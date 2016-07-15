<?php
namespace WorkshopTest;

require_once __DIR__ . '/../Config.php';

use AtomPie\Web\Connection\Http\Header;
use AtomPie\Web\Connection\Http\Request;

class AppTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Cleans up the environment runAfter running a test.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testConstructor_AppShouldNotMutateForTheSameRequest()
    {
        $oConfig = Resource\Config\Config::get();

        $oApp1 = Boot::up($oConfig, 'Class.method');
        $oApp2 = Boot::up($oConfig, 'Class.method');
        $this->assertTrue($oApp1->getDispatcher()->getDispatchManifest()->getEndPoint() == $oApp2->getDispatcher()->getDispatchManifest()->getEndPoint());

    }

}
