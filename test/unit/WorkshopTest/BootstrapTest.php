<?php
namespace WorkshopTest;

use AtomPie\Web\Environment;
use AtomPie\Web\Connection\Http\Header\Status;
use WorkshopTest\Resource\Config\TestConfig;

require_once __DIR__ . '/../../../vendor/autoload.php';

class BootstrapTest extends \PHPUnit_Framework_TestCase
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
        Environment::destroyInstance();
        parent::tearDown();
    }

    public function testBootstrap_EndPoint()
    {

        RequestFactory::produce(
            'DefaultController.index'
        );
        $oResponse = Boot::run(Boot::getEnv(), TestConfig::get());

        $this->assertTrue(
            strstr($oResponse->getContent()->get(),'No') !== false &&
            strstr($oResponse->getContent()->get(), 'Empty') !== false
        );
    }

    public function testBootstrap_UseCase()
    {

        RequestFactory::produce(
            'UseCase.index'
        );
        $oResponse = Boot::run(Boot::getEnv(), TestConfig::get());
        $this->assertTrue($oResponse->getContent()->get() == "[\"data\"]");
    }

    public function testBootstrap_Event()
    {

        RequestFactory::produce(
            'DefaultController.index', 'MockComponent0.Inner.Click'
        );

        $oResponse = Boot::run(Boot::getEnv(), TestConfig::get());
        $this->assertTrue(strstr($oResponse->getContent()->get(),
                'No') !== false and strstr($oResponse->getContent()->get(), 'Yes') !== false);

    }

    public function testBootstrap_Exception()
    {

        RequestFactory::produce(
            'ErrorController.index'
        );
        $oResponse = Boot::run(Boot::getEnv(), TestConfig::get());

        $this->assertTrue($oResponse->getStatus()->is(Status::NOT_FOUND));

    }

}
