<?php
namespace AtomPiePhpUnitTest\Web;

use AtomPie\Web\Connection\Http\Header\ContentType;

class ContentTypeHeaderTest extends \PHPUnit_Framework_TestCase
{
    public function testHeader()
    {
        $oContentTypeHeader = new ContentType('text/html; charset=utf-8');
        $this->assertTrue($oContentTypeHeader->isHtml());
        $this->assertTrue($oContentTypeHeader->getParam('charset') == 'utf-8');
        $this->assertTrue($oContentTypeHeader->getMediaType() == 'text/html');
    }
}
