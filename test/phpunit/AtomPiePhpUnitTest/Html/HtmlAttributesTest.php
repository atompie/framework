<?php
namespace AtomPiePhpUnitTest\Html;

use AtomPie\Html\Exception;
use PHPUnit_Framework_TestCase;
use AtomPie\Html\Attribute;
use AtomPie\Html\Attributes;

class HtmlAttributesTest extends \PHPUnit_Framework_TestCase
{

    public function testAttribute_SetCollection()
    {
        $oAttributes = new Attributes();
        $oAttributes->addAttribute(new Attribute('name1', 'value1'));
        $oAttributes->addAttribute(new Attribute('name2', 'value2'));
        $oAttributes->addAttribute(new Attribute('name3', 'value3'));
        $oAttributes->addAttribute(new Attribute('name4', 'value4'));
        $this->assertTrue($oAttributes == 'name1="value1" name2="value2" name3="value3" name4="value4"');
    }

    public function testAttribute_Remove()
    {
        $oAttributes = new Attributes();
        $oAttributes->addAttribute(new Attribute('name1', 'value1'));
        $oAttributes->addAttribute(new Attribute('name2', 'value2'));
        $oAttributes->addAttribute(new Attribute('name3', 'value3'));
        $oAttributes->addAttribute(new Attribute('name4', 'value4'));
        $oAttributes->removeAttribute('name4');
        $this->assertTrue($oAttributes == 'name1="value1" name2="value2" name3="value3"');
    }

    public function testAttribute_Has()
    {
        $oAttributes = new Attributes();
        $oAttributes->addAttribute(new Attribute('name1', 'value1'));
        $this->assertTrue($oAttributes->hasAttributes());
    }

    public function testAttribute_Get()
    {
        $oAttributes = new Attributes();
        $oAttributes->addAttribute(new Attribute('name1', 'value1'));
        $this->assertTrue($oAttributes->getAttribute('name1') == 'name1="value1"');
    }

    public function testAttribute_ArrayAccess()
    {
        $oAttributes = new Attributes();
        $oAttributes[] = new Attribute('name1', 'value1');
        $oAttributes[] = new Attribute('name2', 'value2');
        $oAttributes[] = new Attribute('name3', 'value3');

        $this->assertTrue($oAttributes == 'name1="value1" name2="value2" name3="value3"');
        $this->assertTrue(isset($oAttributes['name1']));
        $this->assertTrue($oAttributes['name1'] instanceof Attribute);
        $this->assertTrue($oAttributes['name1']->getValue() == 'value1');
        unset($oAttributes['name3']);
        $this->assertTrue($oAttributes == 'name1="value1" name2="value2"');
    }

    public function testAttribute_ArrayAccess_Exception()
    {
        $this->setExpectedException(Exception::class);
        $oAttributes = new Attributes();
        $oAttributes[] = 'aaa';
    }
}

