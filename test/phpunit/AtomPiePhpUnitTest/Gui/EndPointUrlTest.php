<?php
namespace AtomPiePhpUnitTest\Gui;

use AtomPie\Gui\Component\EventUrl;

class EndPointUrlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldToStringEventUrlWithParams()
    {
        $oEventUrl = new EventUrl('class.method', '{MockComponent2.MockComponent21.event}');
        $this->assertTrue($oEventUrl->__toString() == 'class.method{MockComponent2.MockComponent21.event}');
        $oEventUrl->addParam('new', 1);
        $this->assertTrue($oEventUrl->__toString() == 'class.method{MockComponent2.MockComponent21.event}?new=1');
    }

}
