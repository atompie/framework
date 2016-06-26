<?php
namespace WorkshopTest {

    use AtomPie\Gui\Component\ComponentDependencyContainer;
    use AtomPie\Web\Connection\Http\Url\Param\ParamException;
    use AtomPie\Web\Environment;
    use WorkshopTest\Resource\Component\MockComponent0;
    use WorkshopTest\Resource\Component\MockComponent1;
    use WorkshopTest\Resource\Component\MockComponent2;
    use WorkshopTest\Resource\Param\MyParam1;

    require_once __DIR__ . '/../Config.php';
    require_once __DIR__ . '/Resource/Component/MockComponent0.php';
    require_once __DIR__ . '/Resource/Param/MyParam1.php';

    class DependantEventTest extends \PHPUnit_Framework_TestCase
    {

        /**
         * Prepares the environment before running a test.
         */
        protected function setUp()
        {
            $_REQUEST = array();
            Environment::destroyInstance();
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

        public function testComponent_RequestEvent()
        {

            $_REQUEST = array('MyParam1' => 'MyParam1');

            $oComponent = new MockComponent0('Name');
            $oUnitTest = $this;
            $oComponent->handleEvent('event',

                function (MyParam1 $MyParam1 = null) use ($oUnitTest) {
                    // TODO to jest błąd. W zwiazku z tym że jest jeden eventHandler dla wszystkich
                    // TODO komponentów to ta obsługa eventu jest wykonywana dla wszystkich testów
                    // TODO Jeżeli w testach będzie brał udział jeden komponent MockComponent0 o tej samej nazwie to
                    // TODO ten kod wykona się kilka razy.
                    $oUnitTest->assertTrue(true);
                    $oUnitTest->assertFalse($MyParam1->isNull());
                    $oUnitTest->assertTrue($MyParam1->getValue() == 'MyParam1');
                }
            );

            $oComponent->triggerDependentEvent(
                Boot::getComponentDi(),
                ComponentDependencyContainer::EVENT_CLOSURE_ID,
                'event'
            );
        }

        /**
         * @test
         */
        public function testComponent_RequestEvent_TypeLessParam()
        {

            $_REQUEST['param'] = '12345';
            $oComponent = new MockComponent2('Name');
            // Param has not declared type !!!!
            $oThat = $this;
            $oComponent->handleEvent('event', function ($param = null) use ($oThat) {
                $oThat->assertTrue($param == '12345');
            });

            $oComponent->triggerDependentEvent(
                Boot::getComponentDi(),
                ComponentDependencyContainer::EVENT_CLOSURE_ID,
                'event'
            );

        }

        public function testComponent_RequestEvent_MissingParam()
        {

            $_REQUEST = [];

            $oComponent = new MockComponent1('Name');
            /** @noinspection PhpUnusedParameterInspection */
            $oComponent->handleEvent('event', function (MyParam1 $MyParam1) {
                var_dump('222');
            });
            $this->setExpectedException(ParamException::class);

            $oComponent->triggerDependentEvent(
                Boot::getComponentDi(),
                ComponentDependencyContainer::EVENT_CLOSURE_ID,
                'event'
            );

        }

        public function testComponent_RequestEvent_NotMissingParam()
        {

            $_REQUEST = ['param' => '12345'];

            $oComponent = new MockComponent1('Name1');
            $oThat = $this;
            $oComponent->handleEvent('event', function (MyParam1 $param) use ($oThat) {
                $oThat->assertTrue($param->getValue() == '12345');
                $oThat->assertTrue($param->getName() == 'param');
                $oThat->assertTrue($param instanceof MyParam1);

            });

            $oComponent->triggerDependentEvent(
                Boot::getComponentDi(),
                ComponentDependencyContainer::EVENT_CLOSURE_ID,
                'event'
            );

        }
    }
}