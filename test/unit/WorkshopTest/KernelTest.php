<?php
namespace WorkshopTest;

require_once __DIR__ . '/../Config.php';

use AtomPie\Core\Dispatch\QueryString;
use AtomPie\Core\Dispatch\DispatchManifest;
use AtomPie\Core\Dispatch\EndPointImmutable;
use AtomPie\Core\Dispatch\EventSpecImmutable;
use AtomPie\Web\Connection\Http\Request;
use WorkshopTest\Resource\Config\Config;

class KernelTest extends \PHPUnit_Framework_TestCase
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
        $_REQUEST = array();
        parent::tearDown();
    }

    public function testKernel_DispatchManifestFactory()
    {

        $oRequest = RequestFactory::produce(
            QueryString::urlEscape('WorkshopTest\\Resource\\EndPoint\\DefaultController.Method'),
            QueryString::urlEscape('WorkshopTest\\Resource\\Component\\MockComponent0.Name.event')
        );

        $oConfig = Config::get();

        $oDispatchManifest = DispatchManifest::factory(
            $oRequest,
            $oConfig
        );
        $this->assertTrue($oDispatchManifest->getEndPoint() instanceof EndPointImmutable);
        $this->assertTrue($oDispatchManifest->getEventSpec() instanceof EventSpecImmutable);

    }

    public function testKernel_DispatchManifestFactory_OnlyEndPoint()
    {

        $_REQUEST[DispatchManifest::END_POINT_QUERY] = QueryString::urlEscape('WorkshopTest\\Resource\\EndPoint\\DefaultController.Method');

        $oRequest = new Request();
        $oRequest->load();

        $oConfig = Config::get();

        $oDispatchManifest = DispatchManifest::factory(
            $oRequest,
            $oConfig
        );

        $this->assertTrue($oDispatchManifest->getEndPoint() instanceof EndPointImmutable);
        $this->assertTrue($oDispatchManifest->getEventSpec() === null);

    }

    public function testKernel_DispatchManifestFactory_OnlyEndPointAndComponent()
    {

        $_REQUEST[DispatchManifest::END_POINT_QUERY] = QueryString::urlEscape('WorkshopTest\\Resource\\EndPoint\\DefaultController.Method');

        $oRequest = new Request();
        $oRequest->load();

        $oConfig = Config::get();

        $oDispatchManifest = DispatchManifest::factory(
            $oRequest,
            $oConfig
        );

        $this->assertTrue($oDispatchManifest->getEndPoint() instanceof EndPointImmutable);
        $this->assertTrue($oDispatchManifest->getEventSpec() === null);

    }

    public function testKernel_DispatchManifestFactory_DefaultValues()
    {

        $oRequest = new Request();
        $oRequest->load();

        $oConfig = Config::get();

        $oDispatchManifest = DispatchManifest::factory(
            $oRequest,
            $oConfig
        );

        $this->assertTrue($oDispatchManifest->getEndPoint() instanceof EndPointImmutable);
        $this->assertTrue($oDispatchManifest->getEndPoint()->__toString() == 'Default.index');
        $this->assertTrue($oDispatchManifest->getEventSpec() === null);
        $this->assertFalse($oDispatchManifest->hasEventSpec());

    }
}
