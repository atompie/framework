<?php
use Behat\Behat\Context\Context;

class BootstrapContext implements Context
{

    /**
     * @var \AtomPie\Web\Boundary\IAmEnvironment
     */
    private $oEnvironment;

    /**
     * @When /^I bootstrap$/
     */
    public function iBootstrap()
    {
        \AtomPie\Web\Environment::destroyInstance();
    }

    /**
     * @Then /^I have environment instance in Kernel$/
     */
    public function iHaveEnvironmentInstance()
    {
        $this->oEnvironment = \WorkshopTest\Boot::getEnv();
    }

    /**
     * @Given /^Global and Kernel bootstrap environment objects are equal$/
     */
    public function globalAndKernelBootstrapEnvironmentObjectsAreEqual()
    {
        PHPUnit_Framework_Assert::assertTrue($this->oEnvironment === \WorkshopTest\Boot::getEnv());
    }

}