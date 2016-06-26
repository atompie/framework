<?php

class ParamContext extends \GlobalContext
{

    private $aParams = array();

    public function __construct()
    {
        $this->aParams = array();
    }

    /**
     * @Given /^I init endpoint "([^"]*)"$/
     */
    public function iInitEndpoint($arg1)
    {
        $this->oApplication = $this->getApp($arg1);
    }

    /**
     * @When /^I set event listener for dispatcher event "([^"]*)" for param "([^"]*)" interception$/
     * @param $event
     * @param $paramName
     */
    public function iSetEventListenerForDispatcherEventForParamInterception($event, $paramName)
    {

        $this->oApplication->handleEvent(
            $event,
            function ($oApp, \WorkshopTest\Resource\Page\MockPage $oReturnedPage)
            use ($paramName) {

                if (isset($oReturnedPage->Component->$paramName)) {
                    $this->aParams[$paramName] = $oReturnedPage->Component->$paramName;
                }
            }
        );
    }

    /**
     * @Then /^I can read param "([^"]*)" and it equals "([^"]*)"$/
     * @param $param
     * @param $value
     */
    public function iCanReadParamAndItEquals($param, $value)
    {
        PHPUnit_Framework_Assert::assertTrue($this->aParams[$param] == $value);
    }

    /**
     * @Then /^Param "([^"]*)" is not equal "([^"]*)"$/
     * @param $param
     * @param $value
     */
    public function paramIsNotEqual($param, $value)
    {
        PHPUnit_Framework_Assert::assertTrue(isset($this->aParams[$param]) && $this->aParams[$param] != $value);
    }

    /**
     * @Given /^Param "([^"]*)" is not set$/
     * @param $param
     */
    public function paramIsNotSet($param)
    {
        PHPUnit_Framework_Assert::assertTrue(!isset($this->aParams[$param]));
    }

    /**
     * @Then /^When I run application I get exception with message "([^"]*)"\.$/
     * @param $arg1
     */
    public function whenIRunApplicationIGetExceptionWithMessage($arg1)
    {
        try {
            $oConfig = \WorkshopTest\Resource\Config\Config::get();
            $this->oApplication->run($oConfig);
        } catch (\Exception $e) {
            PHPUnit_Framework_Assert::assertTrue($e->getMessage() == $arg1);
        }
    }

    /**
     * @Given /^I exec application$/
     */
    public function iExecApplication()
    {
        $oConfig = \WorkshopTest\Resource\Config\Config::get();
        $this->oApplication->run($oConfig);
    }

}