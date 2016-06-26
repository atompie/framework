<?php
namespace WorkshopTest\Resource\Component;

require_once __DIR__ . '/../Factory/MockComponent3.php';
require_once __DIR__ . '/../Param/MyParam2.php';

use AtomPie\Gui\Component;
use WorkshopTest\Resource\Param\MyParam1;
use WorkshopTest\Resource\Param\MyParam2;

/**
 * Class MockComponent3
 * @package WorkshopTest\Resource\Component
 */
class MockComponent3 extends Component
{

    /**
     * @var MyParam1
     */
    public $oMyParam1;
    /**
     * @var MyParam2
     */
    public $oMyParam2;

    private $sOutput = 'NoEvent';

    public function testEvent(MyParam2 $oMyParam2)
    {
        $this->sOutput = $oMyParam2->getValue();
    }

    /**
     * @param MockComponent3 $oComponent
     * @param MyParam1 $AnnotatedParam1
     * @param MyParam1 $AnnotatedParam2
     */
    public function __factory(MockComponent3 $oComponent, MyParam1 $AnnotatedParam1, MyParam1 $AnnotatedParam2)
    {
        $oComponent->oMyParam1 = $AnnotatedParam1;
        $oComponent->oMyParam2 = $AnnotatedParam2;
    }

}