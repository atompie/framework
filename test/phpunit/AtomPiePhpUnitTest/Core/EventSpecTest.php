<?php
namespace AtomPiePhpUnitTest\Core;

use AtomPie\Core\Dispatch\EndPointException;
use AtomPie\Core\Dispatch\EventSpecImmutable;
use AtomPie\Core\Dispatch\QueryString;

class EventSpecTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function shouldParseEventSpec()
    {
        $sUrl = QueryString::urlEscape('WorkshopTest\\Resource\\Component\\MockComponent0.Name.Event');
        $oEventSpec = new EventSpecImmutable(
            $sUrl,
            [
                "\\WorkshopTest\\Resource\\Component",
                "\\WorkshopTest\\Resource",
                "\\WorkshopTest\\Resource\\EndPoint",
            ],
            [
            ]
        );
        $this->assertTrue($oEventSpec->hasEvent());
        $this->assertTrue('Event' == $oEventSpec->getEvent());
        $this->assertTrue('\WorkshopTest\Resource\Component\MockComponent0' == $oEventSpec->getComponentType());
        $this->assertTrue('Name' == $oEventSpec->getComponentName());
        $this->assertTrue('Event' . EventSpecImmutable::EVENT_SUFFIX == $oEventSpec->getEventMethod());
        $this->assertTrue('WorkshopTest\Resource\Component\MockComponent0.Name.Event' == $oEventSpec->__toString());
    }

    /**
     * @test
     */
    public function shouldParseEventSpecWithShortComponentClass()
    {
        $sUrl = 'MockComponent0.Name.Event';
        $oEventSpec = new EventSpecImmutable(
            $sUrl,
            [
                "\\WorkshopTest\\Resource\\Component",
                "\\WorkshopTest\\Resource",
                "\\WorkshopTest\\Resource\\EndPoint",
            ],
            [
            ]
        );
        $this->assertTrue($oEventSpec->hasEvent());
        $this->assertTrue('Event' == $oEventSpec->getEvent());
        $this->assertTrue('\WorkshopTest\Resource\Component\MockComponent0' == $oEventSpec->getComponentType());
        $this->assertTrue('Name' == $oEventSpec->getComponentName());
        $this->assertTrue('Event' . EventSpecImmutable::EVENT_SUFFIX == $oEventSpec->getEventMethod());
        $this->assertTrue('WorkshopTest\Resource\Component\MockComponent0.Name.Event' == $oEventSpec->__toString());
    }

    /**
     * Event WorkshopTest_Resource_Component_MockComponent0.Name has not 3 parts
     * @test
     */
    public function shouldFailEventSpecWithWrongFormat()
    {
        $this->setExpectedException(EndPointException::class);
        new EventSpecImmutable(
            'WorkshopTest_Resource_Component_MockComponent0.Name',
            [
                "\\WorkshopTest\\Resource\\Component",
                "\\WorkshopTest\\Resource",
                "\\WorkshopTest\\Resource\\EndPoint",
            ],
            [
            ]
        );
    }
//
//    /**
//     * Error: Component class NoClass could not be loaded!
//     *
//     * @test
//     */
//    public function shouldFailEventSpecWhenNoClassExists()
//    {
//        $this->setExpectedException(EndPointException::class);
//        new EventSpecImmutable(
//            'NoClass.Name.Event',
//            [
//                "\\WorkshopTest\\Resource\\Component",
//                "\\WorkshopTest\\Resource",
//                "\\WorkshopTest\\Resource\\EndPoint",
//            ],
//            [
//            ]
//        );
//    }

}
