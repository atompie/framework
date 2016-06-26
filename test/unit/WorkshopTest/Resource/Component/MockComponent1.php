<?php
namespace WorkshopTest\Resource\Component;

require_once __DIR__ . '/../Factory/MockComponent1.php';
require_once __DIR__ . '/../Param/MyParam1.php';
require_once __DIR__ . '/../Param/MyParam2.php';
require_once __DIR__ . '/../Param/MyParam3.php';

use AtomPie\Gui\Component;
use AtomPie\Gui\Component\Annotation\Tag\Template;
use AtomPie\Core\Annotation\Tag\Client;
use AtomPie\Core\Annotation\Tag\EndPoint;
use WorkshopTest\Resource\Param\MyParam1;
use WorkshopTest\Resource\Param\MyParam2;
use WorkshopTest\Resource\Param\MyParam3;

/**
 * Class MockComponent1
 * @package WorkshopTest\Resource\Component
 * @Template(File="WorkshopTest/Resource/Theme/Default/MockComponent1.mustache")
 */
class MockComponent1 extends Component
{

    /**
     * @var MyParam1
     */
    public $oMyParam1;
    /**
     * @var MyParam2
     */
    public $oMyParam2;

    /**
     * @var MyParam3
     */
    public $MyParam3;

    public function test(MyParam3 $MyParam3)
    {

    }

    public function test1($MyParam3)
    {

    }

    /**
     * @Client(Accept="application/json")
     * @EndPoint(ContentType="application/json")
     */
    public function requireJsonContent()
    {
        return 'requireJsonContent';
    }

    /**
     * @EndPoint()
     * @param $test
     * @return string
     */
    public function requireJsonParam($test)
    {
        return 'requireJsonParam';
    }

    /**
     * @EndPoint()
     * @param $test
     * @return string
     */
    public function requireXmlParam($test)
    {
        return 'requireXmlParam';
    }

    /**
     * @param MyParam3 $AnnotatedName
     */
    public function testWithAnnotatedParam(MyParam3 $AnnotatedName)
    {
        $this->MyParam3 = $AnnotatedName;
    }

    public function __factory(MockComponent1 $oComponent, MyParam1 $MyParam1, MyParam2 $MyParam2 = null)
    {
        $oComponent->oMyParam1 = $MyParam1;
        $oComponent->oMyParam2 = $MyParam2;
    }

}