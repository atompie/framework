<?php
use Behat\Behat\Context\Context;

class UrlContext implements Context
{

    /**
     * @var \AtomPie\Core\Dispatch\EndPointImmutable
     */
    private $oEndPoint;

    /**
     * @var \AtomPie\Gui\Component\EventUrl
     */
    private $oUrl;

    /**
     * @Given /^I have EndPoint object which points to "([^"]*)"$/
     * @param $endpoint
     */
    public function iHaveEndPointObjectWhichPointsTo($endpoint)
    {
        $oConfig = \WorkshopTest\Resource\Config\Config::get();
        $this->oEndPoint = new \AtomPie\Core\Dispatch\EndPointImmutable(
            $endpoint,
            $oConfig->getEndPointNamespaces(),
            $oConfig->getEndPointClasses()
        );
    }

    /**
     * @When /^I add event "([^"]*)" to URL from getUrl method$/
     * @param $event
     */
    public function iAddEvent($event)
    {
        $oConfig = \WorkshopTest\Resource\Config\Config::get();

        $oEventSpec = new \AtomPie\Core\Dispatch\EventSpecImmutable(
            $event,
            $oConfig->getEventNamespaces(),
            $oConfig->getEventClasses()
        );

        new \AtomPie\Gui\Component\EventUrl($this->oEndPoint->__toUrlString(), $oEventSpec->__toUrlString());
    }

    /**
     * @Then /^I get URL from EndPoint equal to "([^"]*)"$/
     * @param $url
     */
    public function iGetURLEqualTo($url)
    {
        PHPUnit_Framework_Assert::assertTrue($this->oEndPoint->cloneEndPointUrl()->__toString() == $url);
    }

    /**
     * @When /^I add event "([^"]*)" to URL object$/
     * @param $event
     */
    public function iAddEventToURLObject($event)
    {
        $oConfig = \WorkshopTest\Resource\Config\Config::get();

        $oEventSpec = new \AtomPie\Core\Dispatch\EventSpecImmutable(
            $event,
            $oConfig->getEventNamespaces(),
            $oConfig->getEventClasses()
        );

        $this->oUrl = new \AtomPie\Gui\Component\EventUrl($this->oEndPoint->__toUrlString(),
            $oEventSpec->__toUrlString());
    }

    /**
     * @Then /^I get URL object equal to "([^"]*)"$/
     * @param $url
     */
    public function iGetURLObjectEqualTo($url)
    {
        PHPUnit_Framework_Assert::assertTrue($url == $this->oUrl->__toString());
    }

}