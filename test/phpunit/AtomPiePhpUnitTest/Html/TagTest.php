<?php
namespace AtomPiePhpUnitTest\Html;

use AtomPie\Html\ElementNode;
use AtomPie\Html\Tag\Image;
use AtomPie\Html\Tag\Link;
use AtomPie\Html\Tag\Paragraph;
use AtomPie\Html\Tag\Script;
use AtomPie\Html\TextNode;

class TagTest extends \PHPUnit_Framework_TestCase
{

    public function testScriptPath()
    {
        $oScript = new Script('AtomPiePhpUnitTest\Html\TagTest.php');

        $this->assertTrue($oScript->__toString() == '<script type="text/javascript" src="AtomPiePhpUnitTest\Html\TagTest.php"></script>');
    }

    public function testScriptContent()
    {
        $oScript = new Script(null, 'alert("alert")');
        $this->assertTrue($oScript->__toString() == '<script type="text/javascript">alert("alert")</script>');
    }

    public function testScriptContentAndPath()
    {
        $oScript = new Script('AtomPiePhpUnitTest\Html\TagTest.php', 'alert("alert")');
        $this->assertTrue($oScript->__toString() == '<script type="text/javascript" src="AtomPiePhpUnitTest\Html\TagTest.php">alert("alert")</script>');
    }

    public function testScriptOnCondotion()
    {
        $oScript = new Script('AtomPiePhpUnitTest\Html\TagTest.php');
        $oScript->onCondition('IE');
        $this->assertTrue($oScript->__toString() == '<!--[IE]><script type="text/javascript" src="AtomPiePhpUnitTest\Html\TagTest.php"></script><![endif]-->');
    }

    public function testParagraph()
    {
        $oTag = new Paragraph('Content');
        $this->assertTrue($oTag->__toString() == '<p>Content</p>');
    }

    public function testLink()
    {
        $oTag = new Link('AtomPiePhpUnitTest\Html\TagTest.php');
        $this->assertTrue($oTag->__toString() == '<a href="AtomPiePhpUnitTest\\Html\\TagTest.php" />');

        $oTag = new Link('AtomPiePhpUnitTest\Html\TagTest.php');
        $oTag->setText('TEXT');
        $this->assertTrue($oTag->__toString() == '<a href="AtomPiePhpUnitTest\\Html\\TagTest.php">TEXT</a>');
        $oTag->addChild(new TextNode('Test'));
        $this->assertTrue($oTag->__toString() == '<a href="AtomPiePhpUnitTest\\Html\\TagTest.php">TEXTTest</a>');

        $oTag = new Link('AtomPiePhpUnitTest\Html\TagTest.php', 'TEXT');
        $oTag->setText('Override');
        $this->assertTrue($oTag->__toString() == '<a href="AtomPiePhpUnitTest\\Html\\TagTest.php">Override</a>');

        $oTag = new Link('AtomPiePhpUnitTest\Html\TagTest.php', new ElementNode('br'));
        $this->assertTrue($oTag->__toString() == '<a href="AtomPiePhpUnitTest\\Html\\TagTest.php"><br /></a>');
    }

    public function testImage()
    {
        $oTag = new Image('AtomPiePhpUnitTest\Html\TagTest.php');
        $this->assertTrue($oTag->__toString() == '<img src="AtomPiePhpUnitTest\\Html\\TagTest.php" />');
    }


}