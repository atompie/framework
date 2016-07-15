<?php
namespace WorkshopTest\Resource\Component;

use AtomPie\Gui\Component;
use AtomPie\AnnotationTag\Client;
use AtomPie\Web\Environment;
use AtomPie\AnnotationTag\Template;
use AtomPie\Html\Tag\Head;
use AtomPie\AnnotationTag\EndPoint;
use AtomPie\Web\Connection\Http\Url\Param;

/**
 * @property string EventFlag
 * @property bool $IsFactoryInvoked
 * @property bool IsProcessInvoked
 * @package WorkshopTest\Resource\Component
 * @Template(File="Default/MockComponent0.mustache")
 * @EndPoint(ContentType="application/json")
 * @Client(Type="Cli")
 */
class MockComponent0 extends Component
{

    public $oEnv;
    public $oHead;

    public function __create()
    {
        $this->EventFlag = 'Empty';
    }

    public function __factory(Environment $oEnv)
    {
        $this->IsFactoryInvoked = true;
        $this->oEnv = $oEnv;
    }

    public function __process(Head $oHead)
    {
        $this->IsProcessInvoked = true;
        $this->oHead = $oHead;
    }

    public function clickEvent()
    {
        $this->EventFlag = 'Yes';
    }

    /**
     * @EndPoint()
     * @return MockComponent0
     */
    public static function EndPoint()
    {
        return new self();
    }

}