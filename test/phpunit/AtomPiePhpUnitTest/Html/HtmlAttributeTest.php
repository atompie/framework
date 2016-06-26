<?php
namespace AtomPiePhpUnitTest\Html;

use AtomPie\Html\Attribute;
use AtomPie\Html\ElementNode;

class HtmlAttributeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testAddAttribute()
    {
        $oTag = new ElementNode('img');
        $this->assertTrue($oTag->__toString() == '<img />');
        $oTag->addAttribute(new Attribute('src', 'test'));
        $this->assertTrue($oTag->__toString() == '<img src="test" />');
        $oTag->addAttribute(new Attribute('encoded', '@'));
        $oTag->addAttribute(new Attribute('Encoded', 'Other'));
        $this->assertTrue($oTag->__toString() == '<img src="test" encoded="@" Encoded="Other" />');
    }

    public function testAddNamespacedAttribute()
    {
        $oTag = new ElementNode('img');
        $this->assertTrue($oTag->__toString() == '<img />');
        $oTag->addAttribute(new Attribute('src', 'test', 'my'));
        $this->assertTrue($oTag->__toString() == '<img my:src="test" />');
        $oAttribute = new Attribute('src', 'test');
        $oAttribute->setNamespace('new');
        $this->assertTrue($oAttribute == 'new:src="test"');
        $this->assertTrue($oAttribute->hasNamespace());
    }

    public function testRemoveAttribute()
    {
        $oTag = new ElementNode('img');
        $this->assertTrue($oTag->__toString() == '<img />');
        $oTag->addAttribute(new Attribute('src', 'test'));
        $this->assertTrue($oTag->__toString() == '<img src="test" />');
        $oTag->removeAttribute('src');
        $this->assertTrue($oTag->__toString() == '<img />');
    }

    public function testRemoveNamespacedAttribute()
    {
        $oTag = new ElementNode('img');
        $this->assertTrue($oTag->__toString() == '<img />');
        $oTag->addAttribute(new Attribute('src', 'test', 'my'));
        $this->assertTrue($oTag->__toString() == '<img my:src="test" />');
        $oTag->removeAttribute('my:src');
        $this->assertTrue($oTag->__toString() == '<img />');
    }

    public function testAttributeEntity()
    {
        $oTag = new ElementNode('img');
        $this->assertTrue($oTag->__toString() == '<img />');
        $oTag->addAttribute(new Attribute('amp', '&'));
        $this->assertTrue($oTag->__toString() == '<img amp="&amp;" />');
        $oTag->addAttribute(new Attribute('lt', '<'));
        $this->assertTrue($oTag->__toString() == '<img amp="&amp;" lt="&lt;" />');
        $oTag->addAttribute(new Attribute('gt', '>'));
        $this->assertTrue($oTag->__toString() == '<img amp="&amp;" lt="&lt;" gt="&gt;" />');
    }

    public function testMergeAttribute()
    {
        $oTag = new ElementNode('img');
        $this->assertTrue($oTag->__toString() == '<img />');
        $oTag->addAttribute(new Attribute('src', 'test'));
        $this->assertTrue($oTag->__toString() == '<img src="test" />');
        $oTag->addAttribute(new Attribute('alt', 'a'));
        $this->assertTrue($oTag->__toString() == '<img src="test" alt="a" />');
        $oTag->mergeAttribute(new Attribute('alt', 'b'));
        $this->assertTrue($oTag->__toString() == '<img src="test" alt="a b" />');
        //TODO Do not know if this is correct - maybe it should return <img src="test" alt="a b b" />
        $oTag->mergeAttribute(new Attribute('alt', 'b'));
        $this->assertTrue($oTag->__toString() == '<img src="test" alt="a b" />');
    }

    public function testAttribute_SetValue()
    {
        $oAttribute = new Attribute('src', 'test');
        $oAttribute->setValue('new');
        $this->assertTrue($oAttribute == 'src="new"');
        $this->assertTrue($oAttribute->notEmpty());
        $this->assertTrue($oAttribute->hasValue('new'));
        $oAttribute->removeValue('new');
        $this->assertTrue($oAttribute == 'src=""');
        $this->assertFalse($oAttribute->notEmpty());
        $this->assertFalse($oAttribute->hasValue('new'));
    }

    public function testAttribute_SetName()
    {
        $oAttribute = new Attribute('src', 'test');
        $oAttribute->setName('new');
        $this->assertTrue($oAttribute == 'new="test"');
    }

    public function testAttribute_Encode()
    {
        $oAttribute = new Attribute('src', 'te&st');
        $oAttribute->encode(true);
        $this->assertTrue($oAttribute == 'src="te&amp;st"');
    }

}

