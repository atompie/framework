<?php
use Behat\Behat\Context\Context;

class PageContext implements Context
{

    /**
     * @Given /^Page "([^"]*)" has component "([^"]*)" as property "([^"]*)"$/
     * @param $page
     * @param $component
     * @param $property
     */
    public function pageHasComponentAsProperty($page, $component, $property)
    {
        /** @var \AtomPie\Gui\Page $oPage */
        $oPage = new $page();
        PHPUnit_Framework_Assert::assertTrue($oPage->$property instanceof $component);
    }

    /**
     * @Given /^Page "([^"]*)" has component "([^"]*)" named "([^"]*)" as property "([^"]*)"$/
     * @param $page
     * @param $component
     * @param $componentName
     * @param $property
     */
    public function pageHasComponentNamedAsProperty($page, $component, $componentName, $property)
    {
        /** @var \AtomPie\Gui\Page $oPage */
        $oPage = new $page();
        PHPUnit_Framework_Assert::assertTrue($oPage->$property instanceof $component);
        PHPUnit_Framework_Assert::assertTrue($oPage->$property instanceof \Generi\Boundary\IHaveName);
        PHPUnit_Framework_Assert::assertTrue($oPage->$property->getName() == $componentName);
    }

}