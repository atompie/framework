<?php
use Behat\Behat\Context\Context;

class RequestContext implements Context
{

    public function __construct()
    {
        $_REQUEST = array();
    }

    /**
     * @Given /^I have REQUEST set to value "([^"]*)" = \["([^"]*)"=>"([^"]*)"\]$/
     */
    public function iHaveREQUESTSetToValue($arg1, $arg2, $arg3)
    {
        $_REQUEST[$arg1][$arg2] = $arg3;
    }

    /**
     * @Given /^I have REQUEST set to "([^"]*)"="([^"]*)"$/
     */
    public function iHaveREQUESTSetTo($arg1, $arg2)
    {
        $_REQUEST[$arg1] = $arg2;
    }

    /**
     * @Given /^I set request to have "([^"]*)" header equal to "([^"]*)"$/
     * @param $sHeaderName
     * @param $sHeaderValue
     */
    public function iSetRequestToHaveHeaderEqualTo($sHeaderName, $sHeaderValue)
    {
        $_SERVER['HTTP_' . strtoupper($sHeaderName)] = $sHeaderValue;
    }
}