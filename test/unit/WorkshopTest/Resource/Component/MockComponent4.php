<?php
namespace WorkshopTest\Resource\Component;

use AtomPie\Gui\Component;
use AtomPie\Gui\Component\Annotation\Tag\Template;
use AtomPie\Gui\Component\ComponentParam;
use AtomPie\Html\Tag\Head;
use AtomPie\Web\Connection\Http\Url\Param;
use AtomPie\Web\Connection\Http\Url\Param\Constrain;
use AtomPie\Web\Connection\Http\Url\Param\IConstrain;

class ConstrainedParam extends Param implements IConstrain
{

    /**
     * This method can throw Exception. Value can be read from
     * $this->getValue(). Remember Value can be array or string.
     *
     * If you want to change response status from INTERNAL_SERVER_ERROR
     * @see \AtomPie\Web\Connection\Http\Status\Header pass code with
     * Exception.
     *
     * @return bool
     * @throws Constrain\Exception
     */
    public function validate()
    {
        return is_numeric($this->getValue());
    }
}

class ConstrainedComponentParam extends ComponentParam implements IConstrain
{

    /**
     * This method can throw Exception. Value can be read from
     * $this->getValue(). Remember Value can be array or string.
     *
     * If you want to change response status from INTERNAL_SERVER_ERROR
     * @see \AtomPie\Web\Connection\Http\Status\Header pass code with
     * Exception.
     *
     * @return bool
     * @throws Constrain\Exception
     */
    public function validate()
    {
        return is_numeric($this->getValue());
    }
}

/**
 * @Template(File="Default/MockComponent4.mustache")
 * @property string EventFlag
 * @package WorkshopTest\Resource\Component
 */
class MockComponent4 extends Component
{

    public $Id;
    public $GlobalId;
    public $IntValue;

    public function __create(
        Head $oHead,
        ConstrainedComponentParam $Id = null,
        Param $GlobalId = null,
        ConstrainedParam $IntValue = null
    ) {

        $oHead->addScript('test.js');
        $this->EventFlag = 'Empty';
        if (!$Id->isNull()) {
            $this->Id = $Id->getValue();
        }
        if (!$GlobalId->isNull()) {
            $this->GlobalId = $GlobalId->getValue();
        }
        if (!$IntValue->isNull()) {
            $this->IntValue = $IntValue->getValue();
        }
    }

    public function clickEvent()
    {
        $this->EventFlag = 'Yes';
    }

}