<?php
namespace WorkshopTest;

require_once __DIR__ . '/../Config.php';

use AtomPie\Web\Environment;
use AtomPie\Web\Connection\Http\Header;
use WorkshopTest\Resource\Component\MockComponent7;

class EnvironmentTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        Environment::destroyInstance();
        parent::setUp();
    }

    /**
     * Cleans up the environment runAfter running a test.
     */
    protected function tearDown()
    {
        Boot::getEnv();
        parent::tearDown();
    }

    public function testProducedRequestIsTheSameAsEnvRequest()
    {
        $oEnvironment = Boot::getEnv();

        $sEndPoint = MockComponent7::Type()->getName() . '.EndPoint';
        $oRequest = RequestFactory::produce(
            $sEndPoint,
            null,
            array('Param' => 'Param'));

        $this->assertTrue($oRequest === $oEnvironment->getRequest());
    }

}
