<?php
use Behat\Behat\Context\Context;

class EndPointContext implements Context
{
    /**
     * @Given /^Endpoint "([^"]*)" exists$/
     * @param $endPoint
     */
    public function endpointExists($endPoint)
    {
        PHPUnit_Framework_Assert::assertTrue(class_exists($endPoint));
    }

    /**
     * @Given /^Endpoint "([^"]*)" has action "([^"]*)"$/
     * @param $endPoint
     * @param $action
     */
    public function endpointHasAction($endPoint, $action)
    {
        PHPUnit_Framework_Assert::assertTrue(method_exists($endPoint, $action));
    }

    /**
     * @Given /^Endpoint "([^"]*)" returns page "([^"]*)" through action "([^"]*)"$/
     * @param $endPoint
     * @param $page
     * @param $action
     */
    public function endpointReturnsPageThroughAction($endPoint, $page, $action)
    {
        $oEndPoint = new $endPoint();
        $oOutput = $oEndPoint->$action();

        PHPUnit_Framework_Assert::assertTrue($oOutput instanceof $page);
    }
}