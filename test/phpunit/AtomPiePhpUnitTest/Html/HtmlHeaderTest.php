<?php
namespace AtomPiePhpUnitTest\Html;

use AtomPie\Html\HtmlHeader;

class HtmlHeaderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        HtmlHeader::destroyInstance();
        parent::setUp();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Constructs the test case.
     */
    public function testAppendContent()
    {
        $oHeader = HtmlHeader::getInstance()->getHeadTag();

        $oHeader->addScript('test', 'Content');

        $oHeader->appendContentToScript('test', '-NewContent', 'Id4Text');

        $this->assertTrue($oHeader->getScript('test')->hasChild('Id4Text'));
        $this->assertTrue($oHeader->getScript('test')->getChild('Id4Text') == '-NewContent');
        $this->assertTrue(implode('', $oHeader->getScript('test')->getChildren()) == 'Content-NewContent');
        $this->assertTrue($oHeader->getScript('test')->getAttribute('src')->getValue() == 'test');
    }

    public function testAddScript()
    {
        $oHeader = HtmlHeader::getInstance()->getHeadTag();
        $oHeader->addScript('test', 'Content');
        $String = $oHeader->__toString();
        $this->assertTrue($String == '<head><script type="text/javascript" src="test">Content</script></head>');
    }

    public function testAddDuplicateScript()
    {
        $oHeader = HtmlHeader::getInstance()->getHeadTag();

        $oHeader->addScript('test', 'Content');
        $oHeader->addScript('test', 'Content');
        $String = $oHeader->__toString();
        $this->assertTrue($String == '<head><script type="text/javascript" src="test">Content</script></head>');
    }

    public function testGetScript()
    {
        $oHeader = HtmlHeader::getInstance()->getHeadTag();

        $oHeader->addScript('test', 'Content');
        $this->assertTrue($oHeader->getScript('test')->getAttribute('src')->getValue() == 'test');
    }

    public function testAddCss()
    {
        $oHeader = HtmlHeader::getInstance()->getHeadTag();

        $oHeader->addCss('test');
        $String = $oHeader->__toString();
        $this->assertTrue($String == '<head><link href="test" rel="stylesheet" /></head>');
    }

    public function testGetCss()
    {
        $oHeader = HtmlHeader::getInstance()->getHeadTag();

        $oHeader->addCss('test');
        $this->assertTrue($oHeader->getCss('test')->getAttribute('href')->getValue() == 'test');
    }

    public function testAddDuplicateCss()
    {
        $oHeader = HtmlHeader::getInstance()->getHeadTag();

        $oHeader->addCss('test');
        $oHeader->addCss('test');
        $String = $oHeader->__toString();
        $this->assertTrue($String == '<head><link href="test" rel="stylesheet" /></head>');
    }

    public function testDuplicateDisplayScript()
    {
        $oHeader = HtmlHeader::getInstance()->getHeadTag();

        $oHeader->addScript('test', 'Content');
        $String = $oHeader->__toString();
        $this->assertTrue($String == '<head><script type="text/javascript" src="test">Content</script></head>');
        $oHeader->addScript('test', 'Content');
        $String = $oHeader->__toString();
        $this->assertTrue($String == '<head><script type="text/javascript" src="test">Content</script></head>');

        // Now added new script
        $oHeader->addScript('test1', 'Content');
        $String = $oHeader->__toString();
        $this->assertTrue($String == '<head><script type="text/javascript" src="test">Content</script><script type="text/javascript" src="test1">Content</script></head>');
    }

    public function testDuplicateDisplayCss()
    {
        $oHeader = HtmlHeader::getInstance()->getHeadTag();

        $oHeader->addCss('test');
        $String = $oHeader->__toString();
        $this->assertTrue($String == '<head><link href="test" rel="stylesheet" /></head>');
        $oHeader->addCss('test');
        $String = $oHeader->__toString();
        $this->assertTrue($String == '<head><link href="test" rel="stylesheet" /></head>');
        // Now added new css
        $oHeader->addCss('test1');
        $String = $oHeader->__toString();
        $this->assertTrue($String == '<head><link href="test" rel="stylesheet" /><link href="test1" rel="stylesheet" /></head>');
    }
}

