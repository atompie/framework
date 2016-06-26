<?php
/**
 * Created by PhpStorm.
 * User: risto
 * Date: 11.10.15
 * Time: 15:54
 */
use Behat\Behat\Context\Context;

class EnvironmentContext implements Context
{

    private $oEnv;

    /**
     * @Given /^I destroy environment$/
     */
    public function iDestroyEnvironment()
    {
        \AtomPie\Web\Environment::destroyInstance();
    }

    /**
     * @When /^I get an instance of Environment$/
     */
    public function iGetAnInstanceOfEnvironment()
    {
        $this->oEnv = \WorkshopTest\Boot::getEnv();
    }
}