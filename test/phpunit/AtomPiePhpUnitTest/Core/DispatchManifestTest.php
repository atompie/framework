<?php
namespace AtomPiePhpUnitTest\Core {

    use AtomPie\Core\Dispatch\DispatchManifest;
    use AtomPie\Core\Dispatch\EndPointImmutable;
    use AtomPie\Core\Dispatch\EventSpecImmutable;
    use AtomPiePhpUnitTest\Core\Mock\Config;
    use AtomPiePhpUnitTest\Core\Mock\MockComponent2;

    class DispatchManifestTest extends \PHPUnit_Framework_TestCase
    {

        /**
         * @test
         */
        public function testDispatchManifest_NoComponent()
        {

            $oConfig = Config::get();

            $oDispatchManifest = new DispatchManifest(
                $oConfig,
                new EndPointImmutable(
                    'WorkshopTest_Resource_EndPoint_DefaultController.Method',
                    $oConfig->getEndPointNamespaces(),
                    $oConfig->getEndPointClasses()
                )
            );
            $this->assertFalse($oDispatchManifest->hasEventSpec());
            $this->assertTrue($oDispatchManifest->getEndPoint() instanceof EndPointImmutable);
        }

        /**
         * @test
         */
        public function shouldRenderDispatchManifestToString()
        {

            $oConfig = Config::get();

            $oDispatchManifest = new DispatchManifest(
                $oConfig,
                new EndPointImmutable(
                    'WorkshopTest_Resource_EndPoint_DefaultController.Method',
                    $oConfig->getEndPointNamespaces(),
                    $oConfig->getEndPointClasses()
                ),
                new EventSpecImmutable(
                    'WorkshopTest_Resource_Component_MockComponent0.Name.event',
                    $oConfig->getEventNamespaces(),
                    $oConfig->getEventClasses()
                )
            );
            $this->assertTrue($oDispatchManifest->__toString() == 'DefaultController.Method{MockComponent0.Name.event}');
        }

        /**
         * @test
         */
        public function shouldChangeDispatchManifestOnEventChange()
        {

            $oConfig = Config::get();

            $oDispatchManifest = new DispatchManifest(
                $oConfig,
                new EndPointImmutable(
                    'WorkshopTest_Resource_EndPoint_DefaultController.Method',
                    $oConfig->getEndPointNamespaces(),
                    $oConfig->getEndPointClasses()
                ),
                new EventSpecImmutable(
                    'WorkshopTest_Resource_Component_MockComponent0.Name.event',
                    $oConfig->getEventNamespaces(),
                    $oConfig->getEventClasses()
                )
            );

            $oChanged = $oDispatchManifest->cloneWithEvent(
                new MockComponent2(),
                'Event'
            );
            $this->assertTrue($oChanged->__toString() == 'DefaultController.Method{MockComponent2.2.Event}');

        }

    }

}
