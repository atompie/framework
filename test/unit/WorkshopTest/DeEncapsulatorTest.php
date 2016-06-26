<?php
namespace WorkshopTest;

require_once __DIR__ . '/../Config.php';
require_once __DIR__ . '/Resource/EncapsulatedClass.php';

use AtomPie\Service\Object\DeEncapsulator;
use WorkshopTest\Resource\EncapsulatedClass;

class DeEncapsulatorTest extends \PHPUnit_Framework_TestCase
{

    public function testDeEncapsulator_Properties()
    {
        $oDeEncapsulator = new DeEncapsulator(new EncapsulatedClass());
        $this->assertTrue(isset($oDeEncapsulator->sProperty));
        $this->assertTrue($oDeEncapsulator->sProperty == 1);
        $oDeEncapsulator->sProperty = 3;
        $this->assertTrue($oDeEncapsulator->sProperty == 3);
        unset($oDeEncapsulator->sProperty);
        $this->assertTrue($oDeEncapsulator->sProperty == null);
    }

    public function testDeEncapsulator_Method()
    {
        $oDeEncapsulator = new DeEncapsulator(new EncapsulatedClass());
        $this->assertTrue($oDeEncapsulator->method() == 2);
    }
}
