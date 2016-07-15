<?php
namespace AtomPiePhpUnitTest\Web;

use AtomPie\Web\Connection\Http\Header;
use AtomPie\Web\Exception;

class HeaderTest extends \PHPUnit_Framework_TestCase
{

    public function testHeader()
    {
        $oHeader = new Header(Header::CONTENT_TYPE, 'text/html');
        $this->assertTrue($oHeader->__toString() == 'CONTENT-TYPE: text/html');
    }

    public function testIncorrectHeaderValue()
    {
        $this->expectException(Exception::class);
        new Header(Header::CONTENT_TYPE, array('text/html'));
    }

    public function testIncorrectHeaderName()
    {
        $this->expectException(Exception::class);
        new Header(array(Header::CONTENT_TYPE), 'text/html');
    }
}
