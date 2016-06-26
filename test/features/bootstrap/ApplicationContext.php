<?php
use WorkshopTest\Boot;

class ApplicationContext extends \GlobalContext
{

    /**
     * @var \AtomPie\Web\Connection\Http\Response
     */
    private $oResponse;

    /**
     * @var \AtomPie\Boundary\Core\IAmFrameworkConfig
     */
    private $oConfig;
    /**
     * @var \WorkshopTest\Resource\UseCase\UseCase
     */
    private $oUseCase;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $_REQUEST = array();
        $_SESSION = array();
        $_COOKIE = array();
        $_SESSION = array();
        \AtomPie\Web\Environment::destroyInstance();
    }

    /**
     * @Given /^I init application with default endpoint$/
     */
    public function iInitApplicationWithDefaultEndpoint()
    {
        $this->oApplication = $this->getApp('Default.index');
    }

    /**
     * @Given /^I have \[UseCase\] class with method "([^"]*)" and it has parameter of type MyParam1$/
     * @param $method
     */
    public function iHaveUseCaseClassWithMethodAndItHasParameterOfTypeMyParam($method)
    {
        $this->oUseCase = new \WorkshopTest\Resource\UseCase\UseCase();
        PHPUnit_Framework_Assert::assertTrue(method_exists($this->oUseCase, $method));
    }

    /**
     * @Then /^I get \[UseCase\] property filled with Param value equal to "([^"]*)"="([^"]*)"$/
     * @param $arg1
     * @param $arg2
     */
    public function iGetUseCasePropertyFilledWithParamValueEqualTo($arg1, $arg2)
    {
        $data = $this->oUseCase->oParam;
        PHPUnit_Framework_Assert::assertTrue($data->getName() === $arg1);
        PHPUnit_Framework_Assert::assertTrue($data->getValue() === $arg2);
    }

    /**
     * @When /^I invoke \[UseCase\] method "([^"]*)" via UseCaseMethodInvoker$/
     * @param $arg1
     */
    public function iInvokeUseCaseMethodViaUseCaseInvoker($arg1)
    {
        \WorkshopTest\Boot::getEnv()->getRequest()->getAllParams();
        $oDi = $this->oApplication->getEndPointDependencyContainer();
        $oDependencyInjector = new \AtomPie\DependencyInjection\DependencyInjector($oDi);
        $oDependencyInjector->invokeMethod($this->oUseCase, $arg1);
    }

    /**
     * @Given /^I run application$/
     */
    public function iRunApplication()
    {
        $oConfig = \WorkshopTest\Resource\Config\Config::get();
        $this->oApplication->run($oConfig);
    }

    /**
     * @When /^I execute application$/
     */
    public function iExecuteApplication()
    {
        $oConfig = \WorkshopTest\Resource\Config\Config::get();
        $this->oResponse = $this->oApplication->run($oConfig);
    }

    /**
     * @Then /^Application should return response$/
     */
    public function applicationShouldReturnResponse()
    {
        PHPUnit_Framework_Assert::assertTrue($this->oResponse instanceof \AtomPie\Web\Connection\Http\Response);
    }

    /**
     * @When /^I init application with endpoint spec "([^"]*)"$/
     * @param $endPoint
     */
    public function iRunApplicationWithEndpointSpec($endPoint)
    {
        $this->setApp($endPoint);
    }

    /**
     * @Given /^I init application with endpoint spec "([^"]*)" in namespace "([^"]*)"$/
     * @param $endPointSpec
     * @param $namespace
     */
    public function iInitApplicationWithEndpointSpecInNamespace($endPointSpec, $namespace)
    {
        $this->setApp($endPointSpec, $namespace);
    }

    /**
     * @Given /^I run application endpoint "([^"]*)"$/
     * @param $endPointSpec
     */
    public function iRunApplicationWithEndpointSpecInNamespaceWorkshopTestResourceEndPoint($endPointSpec)
    {
        \WorkshopTest\RequestFactory::produce(
            $endPointSpec
        );
        $this->oResponse = Boot::run(Boot::getEnv(), $this->oConfig);
    }

    /**
     * @Given /^Response content equals "([^"]*)"$/
     * @param $content
     */
    public function responseContentEquals($content)
    {
        $endPointContent = $this->oResponse->getContent()->get();
        PHPUnit_Framework_Assert::assertTrue($endPointContent == $content);
    }

    /**
     * @When /^I run application endpoint "([^"]*)" and event "([^"]*)"$/
     * @param $endPointSpec
     * @param $event
     */
    public function iRunApplicationEndpointAndEvent($endPointSpec, $event)
    {
        \WorkshopTest\RequestFactory::produce(
            $endPointSpec,
            $event
        );
        $this->oResponse = Boot::run(Boot::getEnv(), $this->oConfig);
    }

    /**
     * @Given /^Response status equals "([^"]*)"$/
     * @param $status
     */
    public function responseStatusEquals($status)
    {
        PHPUnit_Framework_Assert::assertTrue($this->oResponse->getStatus()->is($status));
    }

    /**
     * @Given /^I define config as "([^"]*)"$/
     * @param $configClass
     */
    public function iDefineConfigAsWorkshopTestResourceConfigConfig($configClass)
    {
        /**
         * @var $configClass \WorkshopTest\Resource\Config\Config
         */
        $this->oConfig = $configClass::get();
    }

    /**
     * @param $endPointSpec
     * @param $namespace
     * @return \AtomPie\System\Application
     */
    private function setApp($endPointSpec, $namespace = null)
    {

        $oConfig = \WorkshopTest\Resource\Config\Config::get();
        $this->oApplication = \WorkshopTest\Boot::up($oConfig, $endPointSpec, $namespace);
    }

}