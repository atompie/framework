<?php
namespace WorkshopTest\EndPoint {

    use AtomPie\Core\Dispatch\EndPointException;
    use AtomPie\Core\Dispatch\QueryString;
    use AtomPie\System\Application;
    use AtomPie\System\Dispatch\DispatchException;
    use AtomPie\Web\Environment;
    use AtomPie\Web\Connection\Http\Request;
    use WorkshopTest\Boot;
    use WorkshopTest\Resource\Component\MockComponent0;
    use WorkshopTest\Resource\Config\Config;
    use WorkshopTest\Resource\EndPoint\DefaultController;
    use WorkshopTest\Resource\EndPoint\MockInterfaceEndPoint;
    use WorkshopTest\Resource\Operation\UseCaseApi;
    use WorkshopTest\Resource\Page\DefaultPage;

    require_once __DIR__ . '/../../Config.php';

    /**
     * Class DispatcherTest
     * @package WorkshopTest\EndPoint
     */
    class DispatcherTest extends \PHPUnit_Framework_TestCase
    {

        /**
         * Prepares the environment before running a test.
         */
        protected function setUp()
        {
            Environment::destroyInstance();
            $_REQUEST = array();
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

        public function testDispatcher_EndPoint()
        {
            $oConfig = Config::get();
            $oApplication = $this->getApp(DefaultController::Type()->getName() . '.index', $oConfig);

            $oResponse = $oApplication->run($oConfig);
            $this->assertTrue($oResponse->getStatus()->isOk());
            $this->assertTrue($oResponse->getContent()->getContentType()->isHtml());
        }

        public function testDispatcher_EndPoint_SimpleParam()
        {

            $_REQUEST['Name'] = 'Ian';

            $oConfig = Config::get();
            $oApplication = $this->getApp(
                DefaultController::Type()->getName() . '.simpleParam',
                $oConfig
            );

            $oResponse = $oApplication->run($oConfig);
            $this->assertTrue($oResponse->getStatus()->isOk());
            $this->assertTrue($oResponse->getContent()->get() == 'Ian');
            $this->assertTrue($oResponse->getContent()->getContentType()->isHtml());
        }

        public function testDispatcher_EndPoint_AnnotatedMethod()
        {

            $oConfig = Config::get();
            $oApplication = $this->getApp(
                DefaultController::Type()->getName() . '.annotatedMethod',
                $oConfig
            );

            $oResponse = $oApplication->run($oConfig);
            $this->assertTrue($oResponse->getStatus()->isOk());
            $this->assertTrue($oResponse->getContent()->getContentType()->isHtml());
        }

        public function testDispatcher_EndPoint_JsonContentType()
        {

            $oConfig = Config::get();
            $oApplication = $this->getApp(
                DefaultController::Type()->getName() . '.JsonContentType',
                $oConfig
            );

            $oResponse = $oApplication->run($oConfig);
            $this->assertTrue($oResponse->getStatus()->isOk());
            $this->assertTrue($oResponse->getContent()->getContentType()->isJson());
        }

        public function testDispatcher_EndPoint_NoEndPoint()
        {

            $oConfig = Config::get();
            $oApplication = $this->getApp(
                'NO_END_POINT.NO_METHOD',
                $oConfig
            );

            $this->expectException(DispatchException::class);
            $oApplication->run($oConfig);
        }

        public function testDispatcher_EndPoint_NoActionMethod()
        {

            $oConfig = Config::get();
            $oApplication = $this->getApp(
                'DefaultController.NO_METHOD',
                $oConfig
            );

            $this->expectException(DispatchException::class);
            $oApplication->run($oConfig);

        }

        public function testDispatcher_EndPoint_ReturnsNotPageType()
        {

            $oConfig = Config::get();
            $oApplication = $this->getApp(
                DefaultController::Type()->getName() . '.NotPage',
                $oConfig
            );

            $this->expectException(DispatchException::class);
            $oResponse = $oApplication->run($oConfig);
            $this->assertTrue($oResponse->getContent()->getContentLength() == 0);
            $this->assertTrue($oResponse->getContent()->get() == null);
        }

        public function testDispatcher_EndPoint_EmptyAction()
        {
            $this->expectException(EndPointException::class);

            $oConfig = Config::get();
            $oApplication = $this->getApp(
                DefaultController::Type()->getName() . '.',
                $oConfig
            );

            $oApplication->run($oConfig);
        }

        // UseCaseApi

        public function testDispatcher_Operation_Json()
        {

            $oConfig = Config::get();
            $oApplication = $this->getApp(
                UseCaseApi::Type()->getName() . '.getJson',
                $oConfig,
                'WorkshopTest\Resource\Operation'
            );

            $oResponse = $oApplication->run($oConfig);

            $this->assertTrue($oResponse->getStatus()->isOk());
            $this->assertTrue($oResponse->getContent()->getContentType()->isJson());

            $this->assertTrue(json_decode($oResponse->getContent()->get()) == true);
        }

        public function testDispatcher_Operation_XmlFromArray()
        {

            $oConfig = Config::get();
            $oApplication = $this->getApp(
                UseCaseApi::Type()->getName() . '.getArrayAsXml',
                $oConfig,
                'WorkshopTest\Resource\Operation'
            );

            $oResponse = $oApplication->run($oConfig);
            $this->assertTrue($oResponse->getStatus()->isOk());
            $this->assertTrue($oResponse->getContent()->getContentType()->isXml());
            $this->assertTrue(false !== strstr($oResponse->getContent()->get(), '<tag>value</tag>'));
        }

        public function testDispatcher_Operation_XmlFromObject()
        {

            $oConfig = Config::get();
            $oApplication = $this->getApp(
                UseCaseApi::Type()->getName() . '.getObjectAsXml',
                $oConfig,
                'WorkshopTest\Resource\Operation'
            );

            $oResponse = $oApplication->run($oConfig);
            $this->assertTrue($oResponse->getStatus()->isOk());
            $this->assertTrue($oResponse->getContent()->getContentType()->isXml());
            $this->assertTrue(false !== strstr($oResponse->getContent()->get(), '<tag>value</tag>'));
        }

        public function testDispatcher_Operation_TwoContentTypes()
        {

            $oConfig = Config::get();
            $oApplication = $this->getApp(
                UseCaseApi::Type()->getName() . '.twoContentTypes',
                $oConfig,
                'WorkshopTest\Resource\Operation'
            );

            $oResponse = $oApplication->run($oConfig);
            $this->assertTrue($oResponse->getStatus()->isOk());
            $this->assertTrue($oResponse->getContent()->getContentType()->isJson());
        }

        // Accept

        public function testDispatcher_EndPoint_AcceptHeader_InncorectContentType()
        {

            $_SERVER['HTTP_ACCEPT'] = 'text/html';
            // DefaultController.AcceptHeader requires application/json acceptance
            $sEndPoint = DefaultController::Type()->getName() . '.AcceptHeader';

            $oConfig = Config::get();
            $oApplication = $this->getApp(
                $sEndPoint,
                $oConfig
            );

            $this->expectException(DispatchException::class);
            $oApplication->run($oConfig);

        }

        public function testDispatcher_EndPoint_AcceptHeader_CorectContentType()
        {

            $_SERVER['HTTP_ACCEPT'] = 'text/html,application/json';
            $sEndPoint = DefaultController::Type()->getName() . '.AcceptHeader';

            $oConfig = Config::get();
            $oApplication = $this->getApp(
                $sEndPoint,
                $oConfig
            );

            $oApplication->run($oConfig);

        }

        // Event

        public function testDispatcher_EndPoint_CatchEvent()
        {

            $sEndPoint = DefaultController::Type()->getName() . '.index';
            $sEventSpecString = 'MockComponent0.Inner.event';

            $oConfig = Config::get();
            $oApplication = $this->getApp(
                $sEndPoint,
                $oConfig,
                'WorkshopTest\Resource\EndPoint',
                $sEventSpecString
            );

            $oThat = $this;

            $oApplication->handleEvent(Application::EVENT_AFTER_END_POINT_INVOKE,
                function ($oApp, DefaultPage $oEndPointContent) use ($oThat) {
                    $oEndPointContent->Inner->handleEvent('event', function () use ($oThat) {
                        $oThat->assertTrue(true);
                    });
                });

            $oApplication->run($oConfig);

        }

        public function testDispatcher_EndPoint_Default()
        {

            $sEndPoint = MockComponent0::Type()->getName();

            $oConfig = Config::get();
            $oApplication = $this->getApp(
                $sEndPoint,
                $oConfig
            );

            $oResponse = $oApplication->run($oConfig);
            echo $oResponse;
        }


        public function testDispatcher_EndPoint_Event()
        {

            $sEndPoint = DefaultController::Type()->getName() . '.index';
            $sEventSpecString = QueryString::urlEscape('WorkshopTest\\Resource\\Page\\DefaultPage.Top.event');

            $oConfig = Config::get();
            $oApplication = $this->getApp(
                $sEndPoint,
                $oConfig,
                'WorkshopTest\Resource\EndPoint',
                $sEventSpecString
            );

            $oThat = $this;

            $oApplication->handleEvent(Application::EVENT_AFTER_END_POINT_INVOKE,
                function ($oApp, DefaultPage $oEndPointContent) use ($oThat) {
                    $oEndPointContent->handleEvent('event', function () use ($oThat) {
                        $oThat->assertTrue(true);
                    });
                });

            $oResponse = $oApplication->run($oConfig);

            $this->assertTrue($oApplication->getDispatcher()->getDispatchManifest()->hasEventSpec());
            $this->assertTrue($oResponse->getStatus()->isOk());
            $this->assertTrue(strstr($oResponse->getContent()->get(), 'Yes') !== false);

        }

        public function testDispatcher_EndPoint_OutsideComponentEvent()
        {

            $sEndPoint = DefaultController::Type()->getName() . '.index';
            $sEventSpecString = QueryString::urlEscape('WorkshopTest\\Resource\\Component\\MockComponent0.Inner.OutsideEvent');

            $oConfig = Config::get();
            $oApplication = $this->getApp(
                $sEndPoint,
                $oConfig,
                'WorkshopTest\Resource\EndPoint',
                $sEventSpecString
            );

            $oResponse = $oApplication->run($oConfig);
            $this->assertTrue($oApplication->getDispatcher()->getDispatchManifest()->hasEventSpec());
            $this->assertTrue($oResponse->getStatus()->isOk());
            $this->assertTrue(strstr($oResponse->getContent()->get(), 'OutsideYes') !== false);

        }

        public function testDispatcher_EndPoint_NoEvent()
        {

            $sEndPoint = DefaultController::Type()->getName() . '.index';

            $oConfig = Config::get();
            $oApplication = $this->getApp(
                $sEndPoint,
                $oConfig
            );

            $oResponse = $oApplication->run($oConfig);
            $this->assertTrue($oResponse->getStatus()->isOk());
            $this->assertTrue(strstr($oResponse->getContent()->get(), 'No') !== false);

        }


        public function testDispatcher_UseCaseEndPointWithInjectedRepository_Access()
        {
            $sEndPoint = UseCaseApi::Type()->getName() . '.getDataFromRepo';

            $oConfig = Config::get();
            $oApplication = $this->getApp(
                $sEndPoint,
                $oConfig,
                'WorkshopTest\Resource\Operation'
            );

            $oResponse = $oApplication->run($oConfig);
            $oJson = $oResponse->getContent()->decodeAsJson();
            $this->assertTrue(in_array('data', $oJson));
        }

        public function testDispatcher_UseCaseEndPointWithInjectedSession_Access()
        {
            $_SESSION['MySession1'] = 'session-data1';
            $sEndPoint = UseCaseApi::Type()->getName() . '.getDataFromSession';

            $oConfig = Config::get();
            $oApplication = $this->getApp(
                $sEndPoint,
                $oConfig,
                'WorkshopTest\Resource\Operation'
            );

            $oResponse = $oApplication->run($oConfig);
            $oJson = $oResponse->getContent()->decodeAsJson();
            $this->assertTrue($oJson == 'session-data1');
        }

        public function testDispatcher_EndPointWithInjectedHead_In_Component()
        {
            $_SESSION['MySession1'] = 'session-data1';
            $sEndPoint = MockInterfaceEndPoint::Type()->getName() . '.index';

            $oConfig = Config::get();
            $oApplication = $this->getApp(
                $sEndPoint,
                $oConfig
            );
            $oResponse = $oApplication->run($oConfig);
            $sHtml = $oResponse->getContent()->get();
            $this->assertTrue(strstr($sHtml, 'test.js') !== false);
        }

        public function testDispatcher_EndPointWithComponent_WhichDoesNotExits()
        {
            $_SESSION['MySession1'] = 'session-data1';
            $sEndPoint = MockInterfaceEndPoint::Type()->getName() . '.NO_METHOD';

            $oConfig = Config::get();
            $oApplication = $this->getApp(
                $sEndPoint,
                $oConfig
            );

            $this->expectException(DispatchException::class);
            $oApplication->run($oConfig);
        }

        public function testDispatcher_StaticEndPoint()
        {
            $this->assertFalse(MockInterfaceEndPoint::$staticCall);
            $sEndPoint = MockInterfaceEndPoint::Type()->getName() . '.staticCall';

            $oConfig = Config::get();
            $oApplication = $this->getApp(
                $sEndPoint,
                $oConfig
            );

            $oResponse = $oApplication->run($oConfig);
            $this->assertTrue($oResponse->getStatus()->isOk());
            $this->assertTrue(MockInterfaceEndPoint::$staticCall);
        }

        public function testDispatcher_StaticEndPoint_WithParam()
        {
            $sEndPoint = MockInterfaceEndPoint::Type()->getName() . '.staticCall';

            $oConfig = Config::get();
            $oApplication = $this->getApp(
                $sEndPoint,
                $oConfig,
                'WorkshopTest\Resource\EndPoint',
                null,
                array('param' => 'abc')
            );

            $oResponse = $oApplication->run($oConfig);
            $this->assertTrue(Boot::getEnv()->getRequest()->getParam('param') == 'abc');
            $this->assertTrue($oResponse->getStatus()->isOk());
            $this->assertTrue(MockInterfaceEndPoint::$staticCallParam == 'abc');
        }

        /**
         * @param $sEndPoint
         * @param $oConfig
         * @param string $sNamespace
         * @param null $sEventSpec
         * @param null $aParams
         * @return \AtomPie\System\Application
         */
        private function getApp(
            $sEndPoint,
            $oConfig,
            $sNamespace = 'WorkshopTest\Resource\EndPoint',
            $sEventSpec = null,
            $aParams = null
        ) {
            return Boot::up(
                $oConfig,
                $sEndPoint,
                $sEventSpec,
                $aParams
            );
        }

    }

}
