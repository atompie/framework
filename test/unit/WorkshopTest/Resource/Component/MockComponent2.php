<?php
namespace WorkshopTest\Resource\Component;

require_once __DIR__ . '/../Factory/MockComponent2.php';
require_once __DIR__ . '/../Param/MyParam1.php';

use AtomPie\Gui\Component;
use WorkshopTest\Resource\Param\MyParam1;

class MockComponent2 extends Component
{

    /**
     * @var MyParam1
     */
    public $oMyParam1;

}