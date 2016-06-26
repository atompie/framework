<?php
namespace AtomPiePhpUnitTest\Html;

use AtomPie\Html\HtmlHeader;
use AtomPie\Html\PageBottom;

class PageBottomTest extends \PHPUnit_Framework_TestCase
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
    public function testAddContent()
    {
        $oBottomCollection = PageBottom::getInstance()->getScriptCollection();
        $oBottomCollection->addScript(null, 'test');
        $oBottomCollection->addScript(null, 'test');
        $this->assertTrue('<script type="text/javascript">test</script>' == $oBottomCollection->__toString());
    }

}