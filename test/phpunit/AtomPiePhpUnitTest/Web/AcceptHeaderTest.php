<?php
namespace AtomPiePhpUnitTest\Web;


use AtomPie\Web\Connection\Http\Header\Accept;

class AcceptHeaderTest extends \PHPUnit_Framework_TestCase
{
    public function testHeader()
    {
        $oAcceptHeader = new Accept('text/*;level=1');
        $this->assertTrue($oAcceptHeader->willYouAcceptMediaType('text/html')); // Because accepts text/*
        $this->assertFalse($oAcceptHeader->willYouAcceptMediaType('application/json'));
    }

    public function testScenario_2()
    {
        $oAccept = new Accept('text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8');
        $this->assertTrue($oAccept->willYouAcceptMediaType('text/html'));
        $this->assertTrue($oAccept->willYouAcceptMediaType('application/xhtml+xml'));
        $this->assertTrue($oAccept->willYouAcceptMediaType('application/xml'));
        $this->assertTrue($oAccept->willYouAcceptMediaType('text/*')); // Because accepts */*
        $this->assertTrue($oAccept->willYouAcceptMediaType('application/json')); // because accepts */*
    }

    public function testScenario_3()
    {
        $oAccept = new Accept('*/*');
        $this->assertTrue($oAccept->willYouAcceptMediaType('text/html'));
        $this->assertTrue($oAccept->willYouAcceptMediaType('application/xhtml+xml'));
        $this->assertTrue($oAccept->willYouAcceptMediaType('application/xml'));
        $this->assertTrue($oAccept->willYouAcceptMediaType('text/*')); // Because accepts */*
        $this->assertTrue($oAccept->willYouAcceptMediaType('application/json')); // because accepts */*
    }

    public function testScenario_4()
    {
        $oAccept = new Accept('*/*');
        $this->assertFalse($oAccept->willYouAcceptMediaType('application/json', true));
    }

    public function testScenario_5()
    {
        $oAccept = new Accept('text/html');
        $this->assertFalse($oAccept->willYouAcceptMediaType('application/json'));
    }

    public function testExplicitAccept()
    {
        $oAccept = new Accept('text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8');
        $this->assertTrue($oAccept->willYouAcceptMediaType('text/html', true));
        $this->assertTrue($oAccept->willYouAcceptMediaType('application/xhtml+xml', true));
        $this->assertTrue($oAccept->willYouAcceptMediaType('application/xml', true));
        $this->assertFalse($oAccept->willYouAcceptMediaType('text/*', true));
        $this->assertFalse($oAccept->willYouAcceptMediaType('application/json', true));
    }
}
