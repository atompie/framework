<?php
namespace WorkshopTest;

use AtomPie\Gui\Component\PlaceHolder;

require_once __DIR__ . '/../Config.php';

class PlaceHolderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \AtomPie\Html\Exception
     */
    public function testError()
    {
        $oObject = new PlaceHolder();
        $oObject->property1;
    }

    public function testSetAll()
    {
        $oObject = new PlaceHolder();
        $oObject->setPlaceHolders(array('property1' => 1));
        $this->assertTrue($oObject->property1 == 1);
        $oObject->mergePlaceHolders(array('property2' => 2));
        $this->assertTrue($oObject->property1 == 1);
        $this->assertTrue($oObject->property2 == 2);
    }

    public function testSettersGetters()
    {
        $oObject = new PlaceHolder();
        $oObject->property1 = 1;
        $this->assertTrue($oObject->property1 == 1);
    }

    public function testIssetUnset()
    {
        $oObject = new PlaceHolder();
        $this->assertFalse(isset($oObject->property1));
        $oObject->property1 = 1;
        $this->assertTrue($oObject->property1 == 1);
        unset($oObject->property1);
        $this->assertFalse(isset($oObject->property1));
    }
}
 