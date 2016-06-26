<?php
namespace AtomPiePhpUnitTest\Web;

use AtomPie\Web\Connection\Http\Header\AcceptMediaTypesArray;
use AtomPie\Web\Connection\Http\Header;

class AcceptHeaderMediaTypeArrayTest extends \PHPUnit_Framework_TestCase
{

    public function testParsing()
    {
        $oAcceptHeader = new AcceptMediaTypesArray('text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8');
        $this->assertTrue('text/html' == $oAcceptHeader[0]);
        $this->assertTrue('application/xhtml+xml' == $oAcceptHeader[1]);
        $this->assertTrue('application/xml;q=0.9' == $oAcceptHeader[2]);
        $this->assertTrue('*/*;q=0.8' == $oAcceptHeader[3]);
    }

    public function testParsingMediaTypes_Scenario1()
    {
        /** @var Header\MediaType[] $oAcceptHeader */
        $oAcceptHeader = new AcceptMediaTypesArray('text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8');
        $this->assertTrue('text/html' == $oAcceptHeader[0]->getMedia());
        $this->assertTrue('application/xhtml+xml' == $oAcceptHeader[1]->getMedia());
        $this->assertTrue('application/xml' == $oAcceptHeader[2]->getMedia());
        $this->assertTrue('*/*' == $oAcceptHeader[3]->getMedia());
    }

    public function testParsingMediaTypes_Scenario2()
    {
        /** @var Header\MediaType[] $oAcceptHeader */
        $oAcceptHeader = new AcceptMediaTypesArray('text/*, text/html, text/html;level=1, */*');
        $this->assertTrue('text/html;level=1' == $oAcceptHeader[0]);
        $this->assertTrue('text/html' == $oAcceptHeader[1]);
        $this->assertTrue('text/*' == $oAcceptHeader[2]);
        $this->assertTrue('*/*' == $oAcceptHeader[3]);
    }

    public function testParsingMediaTypes_Scenario3()
    {
        /** @var Header\MediaType[] $oAcceptHeader */
        $oAcceptHeader = new AcceptMediaTypesArray('text/*;q=0.3, text/html;q=0.7, text/html;level=1, text/html;level=2;q=0.4, */*;q=0.5');
        $this->assertTrue('text/html;level=1' == $oAcceptHeader[0]);
        $this->assertTrue('text/html;q=0.7' == $oAcceptHeader[1]);
        $this->assertTrue('*/*;q=0.5' == $oAcceptHeader[2]);
        $this->assertTrue('text/html;level=2;q=0.4' == $oAcceptHeader[3]);
        $this->assertTrue('text/*;q=0.3' == $oAcceptHeader[4]);
    }

    public function testIsMediaType()
    {
        /** @var Header\MediaType[] $oAcceptHeader */
        $oAcceptHeader = new AcceptMediaTypesArray('text/*, text/html, text/html;level=1, */*');
        $this->assertTrue('text/html;level=1' == $oAcceptHeader[0]);
        $this->assertTrue($oAcceptHeader[0]->willYouAccept('text/html'));
        $this->assertTrue($oAcceptHeader[0]->willYouAccept('text/html;charset=utf-8'));
        $this->assertFalse($oAcceptHeader[0]->willYouAccept('text/*')); // it accepts text/html not any text
        $this->assertFalse($oAcceptHeader[0]->willYouAccept('*/*'));
        $this->assertFalse($oAcceptHeader[0]->willYouAccept('*/html'));
        $this->assertTrue('text/*' == $oAcceptHeader[2]);
        $this->assertTrue($oAcceptHeader[2]->willYouAccept('text/html')); // it accepts text/* so text/html as well
        $this->assertFalse($oAcceptHeader[2]->willYouAccept('*/*'));
        $this->assertFalse($oAcceptHeader[2]->willYouAccept('*/html'));

    }

    public function testIsExplicitMediaType()
    {
        /** @var Header\MediaType[] $oAcceptHeader */
        $oAcceptHeader = new AcceptMediaTypesArray('text/*, text/html, text/html;level=1, */*');
        $this->assertTrue($oAcceptHeader[3]->willYouAccept('application/json')); // accepts */* so accepts application/json
        $this->assertFalse($oAcceptHeader[3]->willYouAccept('application/json',
            true)); // accepts */* so accepts application/json
    }

    public function testIncorrectMediaType_Scenario1()
    {
        /** @var Header\MediaType[] | AcceptMediaTypesArray $oAcceptHeader */
        $this->setExpectedException(\AtomPie\Web\Exception::class);
        new AcceptMediaTypesArray('text');
    }

    public function testIncorrectMediaType_Scenario2()
    {
        /** @var Header\MediaType[] | AcceptMediaTypesArray $oAcceptHeader */
        $this->setExpectedException(\AtomPie\Web\Exception::class);
        new AcceptMediaTypesArray('text/html; text/plain');
    }

    public function testIncorrectMediaType_Scenario3()
    {
        /** @var Header\MediaType[] | AcceptMediaTypesArray $oAcceptHeader */
        $this->setExpectedException(\AtomPie\Web\Exception::class);
        new AcceptMediaTypesArray('text; text/plain');
    }


}
