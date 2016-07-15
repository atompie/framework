<?php
namespace WorkshopTest;

use PHPUnit_Framework_TestCase;
use AtomPie\Html\ElementNode;
use AtomPie\Html\Exception;

require_once __DIR__ . '/../Config.php';

/**
 * HtmlElementNode test case.
 */
class HtmlElementNodeInViewTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Cleans up the environment runAfter running a test.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testElementNodeWithInnerHtml()
    {
        $oPTag = new ElementNode('p');
        $oPTag->addInnerHtmlChild('<a>name</a>');
        $this->assertTrue($oPTag == '<p><a>name</a></p>');

        $oPTag->addInnerHtmlChild('<a>name</a>', 'Index');
        $this->assertTrue($oPTag == '<p><a>name</a><a>name</a></p>');
        $oPTag->removeChild('Index');
        $this->assertTrue($oPTag == '<p><a>name</a></p>');

        $oPTag = new ElementNode('p');
        $oPTag->addInnerHtmlChild('a a');

        $this->assertTrue($oPTag == '<p>a a</p>');

        $this->expectException(Exception::class);
        $oPTag->addInnerHtmlChild(new \stdClass());
    }

}

