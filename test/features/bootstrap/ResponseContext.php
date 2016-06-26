<?php
use Behat\Behat\Context\Context;
use WorkshopTest\Boot;

class ResponseContext implements Context
{
    /**
     * @Then /^response has "([^"]*)" header$/
     * @param $sHeaderName
     */
    public function responseHasHeader($sHeaderName)
    {
        $oResponse = Boot::getEnv()->getResponse();
        PHPUnit_Framework_Assert::assertTrue($oResponse->hasHeader($sHeaderName));
    }

    /**
     * @Given /^response "([^"]*)" header is equal to "([^"]*)"$/
     */
    public function responseHeaderIsEqualTo($arg1, $arg2)
    {
        $oResponse = Boot::getEnv()->getResponse();
        $sHeader = $oResponse->getHeader($arg1)->getValue();
        PHPUnit_Framework_Assert::assertTrue($sHeader == $arg2);
    }

    /**
     * @Given /^response Content has header Content\-type is equal to "([^"]*)"$/
     */
    public function responseContentHasHeaderContentTypeIsEqualTo($arg1)
    {
        $oResponse = Boot::getEnv()->getResponse();
        PHPUnit_Framework_Assert::assertTrue($oResponse->getContent()->getContentType()->getValue() == $arg1);
    }

    /**
     * @Given /^response Content-Type header media type equals "([^"]*)"$/
     */
    public function responseHeaderMediaTypeEquals($arg1)
    {
        $oResponse = Boot::getEnv()->getResponse();
        /** @var \AtomPie\Web\Connection\Http\Header\ContentType $oContentType */
        $oContentType = $oResponse->getHeader(\AtomPie\Web\Connection\Http\Header::CONTENT_TYPE);
        PHPUnit_Framework_Assert::assertTrue($oContentType->getMediaType() == $arg1);

    }
}