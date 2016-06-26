<?php
namespace AtomPiePhpUnitTest\Html;

use AtomPie\Html\Attribute;
use AtomPie\Html\CharacterDataNode;
use AtomPie\Html\CommentNode;
use AtomPie\Html\ElementNode;
use AtomPie\Html\TextNode;

/**
 * HtmlElementNode test case.
 */
class HtmlElementNodeTest extends \PHPUnit_Framework_TestCase
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

    public function testChildlessElementNode()
    {
        $oTag = new ElementNode('a');
        $this->assertTrue($oTag->__toString() == '<a />');
        $oTag->setTagName('img');
        $this->assertTrue($oTag->__toString() == '<img />');
        $oTag->addAttribute(new Attribute('src', 'test'));
        $this->assertTrue($oTag->__toString() == '<img src="test" />');
        $oTag->addAttribute(new Attribute('encoded', 'xxx'));
        $this->assertTrue($oTag->__toString() == '<img src="test" encoded="xxx" />');
    }

    public function testElementNodeWithChild()
    {
        $oATag = new ElementNode('a');
        $oATag->addAttribute(new Attribute('href', '#'));
        $this->assertTrue($oATag->__toString() == '<a href="#" />');


        $oImgTag = new ElementNode('img');
        $oImgTag->addAttribute(new Attribute('src', 'test'));
        $this->assertTrue($oImgTag->__toString() == '<img src="test" />');

        $oATag->addChild($oImgTag);

        $this->assertTrue($oATag->__toString() == '<a href="#"><img src="test" /></a>');
    }

    public function testElementNodeWithRemovedChild()
    {

        $oATag = new ElementNode('a');
        $oATag->addAttribute(new Attribute('href', '#'));
        $this->assertTrue($oATag->__toString() == '<a href="#" />');


        $oImgTag = new ElementNode('img');
        $oImgTag->addAttribute(new Attribute('src', 'test'));
        $this->assertTrue($oImgTag->__toString() == '<img src="test" />');

        // Remove all

        $oATag->addChild($oImgTag);
        $this->assertTrue($oATag->__toString() == '<a href="#"><img src="test" /></a>');
        $oATag->removeChildren();
        $this->assertTrue($oATag->__toString() == '<a href="#" />');

        // Selective remove

        $oATag->addChild($oImgTag, 'first');

        $oPTag = new ElementNode('p');
        $oPTag->addAttribute(new Attribute('src', 'test'));
        $this->assertTrue($oPTag->__toString() == '<p src="test" />');

        $oATag->addChild($oPTag, 'second');
        $this->assertTrue($oATag->__toString() == '<a href="#"><img src="test" /><p src="test" /></a>');
        $oATag->removeChild('first');
        $this->assertTrue($oATag->__toString() == '<a href="#"><p src="test" /></a>');

        // Remove namespaced

        $oNamespacedPTag = new ElementNode('p', 'my');
        $oNamespacedPTag->addAttribute(new Attribute('src', 'test'));
        $this->assertTrue($oNamespacedPTag->__toString() == '<my:p src="test" />');
        $oATag->addChild($oNamespacedPTag);
        $this->assertTrue($oATag->__toString() == '<a href="#"><p src="test" /><my:p src="test" /></a>');
        $oATag->removeChildren('my');
        $this->assertTrue($oATag->__toString() == '<a href="#"><p src="test" /></a>');

    }

    public function testElementNodeWithTextChild()
    {
        $oNamespacedPTag = new ElementNode('p', 'my');
        $oNamespacedPTag->addAttribute(new Attribute('src', 'test'));
        $this->assertTrue($oNamespacedPTag->__toString() == '<my:p src="test" />');
        $oNamespacedPTag->addChild(new TextNode('test'));
        $this->assertTrue($oNamespacedPTag->__toString() == '<my:p src="test">test</my:p>');

        $oPTag = new ElementNode('p');
        $oPTag->addAttribute(new Attribute('src', 'test'));
        $this->assertTrue($oPTag->__toString() == '<p src="test" />');
        $oPTag->addChild(new TextNode('test'));
        $this->assertTrue($oPTag->__toString() == '<p src="test">test</p>');
    }

    public function testElementNodeWithCData()
    {
        $oNamespacedPTag = new ElementNode('p', 'my');
        $oNamespacedPTag->addAttribute(new Attribute('src', 'test'));
        $this->assertTrue($oNamespacedPTag->__toString() == '<my:p src="test" />');
        $oNamespacedPTag->addChild(new CharacterDataNode('test'));
        $this->assertTrue($oNamespacedPTag->__toString() == '<my:p src="test"><![CDATA[test]]></my:p>');
    }

    public function testElementNodeCommentNode()
    {
        $oNamespacedPTag = new ElementNode('p', 'my');
        $oNamespacedPTag->addAttribute(new Attribute('src', 'test'));
        $this->assertTrue($oNamespacedPTag->__toString() == '<my:p src="test" />');
        $oNamespacedPTag->addChild(new CommentNode('comment'));
        $this->assertTrue($oNamespacedPTag->__toString() == '<my:p src="test"><!--comment--></my:p>');
    }

    public function testgetElementsTreeByNamespace()
    {
        $oNamespacedATag = new ElementNode('a', 'my');
        $oNamespacedBTag = new ElementNode('b', 'my');

        $oNamespacedPTag = new ElementNode('p', 'my');
        $oNamespacedPTag->addAttribute(new Attribute('src', 'test'));
        $oNamespacedPTag->addChild($oNamespacedATag);
        $oNamespacedPTag->addChild($oNamespacedATag);
        $oNamespacedPTag->addChild($oNamespacedBTag);

        $oNodes = $oNamespacedPTag->getElementsTreeByNamespace('my');

        $this->assertTrue((string)$oNodes[0] == $oNamespacedATag->__toString());
        $this->assertTrue((string)$oNodes[1] == $oNamespacedATag->__toString());
        $this->assertTrue((string)$oNodes[2] == $oNamespacedBTag->__toString());
    }

//	public function testGetElementsTreeByTagName() {
//		$oMyBTag = new ElementNode('b','my');
//		$oATag = new ElementNode('a');
//		$oATag->addChild($oMyBTag);
//		$oBTag = new ElementNode('b');
//		$oMyATag = new ElementNode('a','my');
//
//		$oPTag = new ElementNode('p', 'my');
//		$oPTag->addAttribute(new Attribute('src', 'test'));
//		$oPTag->addChild($oATag);
//		$oPTag->addChild($oATag);
//		$oPTag->addChild($oBTag);
//		$oPTag->addChild($oMyATag);
//		$oPTag->addChild($oMyBTag);
//		$oPTag->addChild($oMyBTag);
//
//		$oNodes = $oPTag->getElementsTreeByTagName('a');
////echo (string)$oNodes[1];
////		echo $oATag->__toString();
////		$this->assertTrue((string)$oNodes[0] == $oATag->__toString());
////		$this->assertTrue((string)$oNodes[1] == $oATag->__toString());
//
//		$oNodes = $oPTag->getElementsTreeByTagName('b');
//
//		$this->assertTrue((string)$oNodes[0] == $oBTag->__toString());
//
//		$oNodes = $oPTag->getElementsTreeByTagName('my:b');
//var_dump(implode(',',$oNodes));
//		$this->assertTrue((string)$oNodes[0] == $oMyBTag->__toString());
//		$this->assertTrue((string)$oNodes[1] == $oMyBTag->__toString());
//
//		$oNodes = $oPTag->getElementsTreeByTagName('my:a');
//
//		$this->assertTrue((string)$oNodes[0] == $oMyATag->__toString());
//	}

}

