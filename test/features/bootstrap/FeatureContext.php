<?php
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use AtomPie\Core\Dispatch\DispatchManifest;

require_once __DIR__ . '/../../../test/unit/Config.php';

/**
 * Defines application features from the specific context.
 *
 * @Scenario /^Kernel init$/
 */
class FeatureContext implements Context, SnippetAcceptingContext
{

    /**
     * @var \AtomPie\Web\Connection\Http\Response
     */
    private $aServer = array();
    private $aRequest = array();
    /**
     * @var \AtomPie\System\Application
     */
    private $oApplication;

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
     * @When /^I run default kernel init$/
     */
    public function iRunDefaultKernelInit()
    {
        $oConfig = \WorkshopTest\Resource\Config\Config::get();
        $this->oApplication = \WorkshopTest\Boot::up($oConfig);
    }

    /**
     * @Then /^I get default dispatch object with default endpoint "([^"]*)"$/
     * @param $sDefaultManifestString
     */
    public function iGetDefaultDispatchObjectWithDefaultEndpoint($sDefaultManifestString)
    {
        PHPUnit_Framework_Assert::assertTrue($sDefaultManifestString == $this->oApplication->getDispatcher()->getDispatchManifest()->getEndPoint()->__toString());
    }

    /**
     * @Given /^I have set _REQUEST, _SERVER, _ENV$/
     */
    public function iHaveSet_REQUEST_SERVER_ENV()
    {
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['CONTEXT_DOCUMENT_ROOT'] = '/var/www';
        $_SERVER['DOCUMENT_ROOT'] = '/var/log';
        $_SERVER['SERVER_ADMIN'] = 'webmaster@localhost';
        $_SERVER['SCRIPT_FILENAME'] = '/var/www/i.php';
        $_SERVER['REMOTE_PORT'] = '34532';
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['SERVER_ADDR'] = '127.0.0.1';
        $_SERVER['SERVER_PORT'] = '8080';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['QUERY_STRING'] = 'XDEBUG_SESSION_START=qwerty';
        $_SERVER['REQUEST_URI'] = '/Folder/i.php?XDEBUG_SESSION_START=qwerty';
        $_SERVER['SCRIPT_NAME'] = '/Folder/i.php';
        unset($_SERVER['PHP_SELF']);

        $this->aServer = $_SERVER;

    }

    /**
     * @Then /^I get access to object of request$/
     */
    public function iGetAccessToObjectOfRequest()
    {
        PHPUnit_Framework_Assert::assertTrue(\WorkshopTest\Boot::getEnv()->getRequest() instanceof \AtomPie\Web\Connection\Http\Request);
    }

    /**
     * @Given /^Request end point query string is "([^"]*)"$/
     * @param $EndPointQueryString
     */
    public function requestEndPointQueryStringIs($EndPointQueryString)
    {
        $this->aRequest = array();
        $this->aRequest[DispatchManifest::END_POINT_QUERY] = $EndPointQueryString;
    }

    /**
     * @Given /^Request has endpoint value "([^"]*)"$/
     * @param $value
     */
    public function requestHasEndpointValue($value)
    {
        $i = \WorkshopTest\Boot::getEnv()->getRequest();
        $i->load();
        PHPUnit_Framework_Assert::assertTrue($i->getParam(DispatchManifest::END_POINT_QUERY) == $value);
    }

    /**
     * @Given /^I get access to response$/
     */
    public function iGetAccessToResponse()
    {
        PHPUnit_Framework_Assert::assertTrue(\WorkshopTest\Boot::getEnv()->getResponse() instanceof \AtomPie\Web\Connection\Http\Response);
    }

    /**
     * @Given /^I get access to server$/
     */
    public function iGetAccessToServer()
    {
        PHPUnit_Framework_Assert::assertTrue(\WorkshopTest\Boot::getEnv()->getServer() instanceof \AtomPie\Web\Server);
    }

    /**
     * @When /^I run kernel init within context of request, server, and env$/
     */
    public function iRunKernelInitWithinContextOfRequestServerAndEnv()
    {
        $_SERVER = $this->aServer;
        $_REQUEST = $this->aRequest;
        $oConfig = \WorkshopTest\Resource\Config\Config::get();
        \WorkshopTest\Boot::up($oConfig, $_REQUEST[DispatchManifest::END_POINT_QUERY]);
    }

    /**
     * @Given /^I have global SESSION variable set to "([^"]*)"="([^"]*)"$/
     * @param $name
     * @param $value
     */
    public function iHaveGlobalSESSIONVariableSetTo($name, $value)
    {
        $_SESSION = array();
        $_SESSION[$name] = $value;
    }

    /**
     * @Then /^I can read session from environment$/
     */
    public function iCanReadSessionFromApplicationObject()
    {
        PHPUnit_Framework_Assert::assertTrue(\WorkshopTest\Boot::getEnv()->getSession() instanceof \AtomPie\Web\Boundary\IAmSession);
    }

    /**
     * @Given /^session variable from environment equals "([^"]*)"="([^"]*)"$/
     * @param $name
     * @param $value
     */
    public function sessionVariableFromApplicationEquals($name, $value)
    {
        PHPUnit_Framework_Assert::assertTrue(\WorkshopTest\Boot::getEnv()->getSession()->get($name) == $value);
    }

    /**
     * @Then /^I can set session via environment$/
     */
    public function iCanSetSessionViaKernel()
    {
        \WorkshopTest\Boot::getEnv()->getSession()->set('test', 1);
    }

    /**
     * @Given /^it lands in global SESSION variable$/
     */
    public function itLandsInGlobalSESSIONVariable()
    {
        PHPUnit_Framework_Assert::assertTrue($_SESSION['test'] == 1);
    }

    /**
     * @Given /^I have \[UseCase\] class with method "([^"]*)" and it has parameter of type Repository$/
     * @param $method
     */
    public function iHaveUseCaseWithMethodAndParameterOfTypeRepository($method)
    {
        $oUseCase = new \WorkshopTest\Resource\UseCase\UseCase();
        PHPUnit_Framework_Assert::assertTrue(method_exists($oUseCase, $method));
    }
//
//	/**
//	 * @Then /^I get \[UseCase\] property filled with repository object$/
//	 */
//	public function iGetUseCaseMethodFilledWithRepositoryObject() {
//		$data = $this->oUseCase->aData;
//		PHPUnit_Framework_Assert::assertTrue(in_array('data',$data));
//	}
//
//	/**
//	 * @Given /^I have \[UseCase\] class with method "([^"]*)" and it has parameter of type Session$/
//	 * @param $method
//	 */
//	public function iHaveUseCaseWithMethodAndItHasParameterOfTypeSession($method) {
//		$this->oUseCase = new \WorkshopTest\Resource\UseCase\UseCase();
//		PHPUnit_Framework_Assert::assertTrue(method_exists($this->oUseCase, $method));
//	}

//	/**
//	 * @Then /^I get \[UseCase\] property filled with session value equal to "([^"]*)"$/
//	 * @param $arg1
//	 */
//	public function iGetUseCaseMethodFilledWithSessionValueEqualTo($arg1) {
//		$data = $this->oUseCase->sSession;
//		PHPUnit_Framework_Assert::assertTrue($data == $arg1);
//	}

    /**
     * @Given /^I have global COOKIE variable set to "([^"]*)"="([^"]*)"$/
     * @param $arg1
     * @param $arg2
     */
    public function iHaveGlobalCOOKIEVariableSetTo($arg1, $arg2)
    {
        $_COOKIE[$arg1] = $arg2;
    }

//	/**
//	 * @Given /^I have \[UseCase\] class with method "([^"]*)" and it has parameter of type CookieJar$/
//	 * @param $method
//	 */
//	public function iHaveUseCaseWithMethodAndItHasParameterOfTypeCookie($method) {
//		$this->oUseCase = new \WorkshopTest\Resource\UseCase\UseCase();
//		PHPUnit_Framework_Assert::assertTrue(method_exists($this->oUseCase, $method));
//	}

//	/**
//	 * @Then /^I get \[UseCase\] property filled with Cookie value equal to "([^"]*)"="([^"]*)"$/
//	 * @param $arg1
//	 * @param $arg2
//	 */
//	public function iGetUseCaseMethodFilledWithCookieValueEqualTo($arg1, $arg2) {
//		$data = $this->oUseCase->oCookieJar->get($arg1)->getValue();
//		PHPUnit_Framework_Assert::assertTrue($data == $arg2);
//	}
//
//	/**
//	 * @Given /^I have \[UseCase\] class with method "([^"]*)" and it has parameter of type SessionJar$/
//	 * @param $method
//	 */
//	public function iHaveUseCaseWithMethodAndItHasParameterOfTypeSessionJar($method) {
//		$this->oUseCase = new \WorkshopTest\Resource\UseCase\UseCase();
//		PHPUnit_Framework_Assert::assertTrue(method_exists($this->oUseCase, $method));
//	}
//
//	/**
//	 * @Then /^I get \[UseCase\] property with SessionJar and session value is equal to "([^"]*)"="([^"]*)"$/
//	 * @param $arg1
//	 * @param $arg2
//	 */
//	public function iGetUseCaseMethodWithSessionJarAndSessionValueIsEqualTo($arg1, $arg2) {
//		$data = $this->oUseCase->oSession->get($arg1)->getValue();
//		PHPUnit_Framework_Assert::assertTrue($data == $arg2);
//	}

    /**
     * @Given /^I have empty SESSION$/
     */
    public function iHaveEmptySESSION()
    {
        $_SESSION = array();
    }

//	/**
//	 * @Then /^I get \[UseCase\] property filled with session value equal to NULL$/
//	 */
//	public function iGetUseCaseMethodFilledWithSessionValueEqualToNULL() {
//		$data = $this->oUseCase->sSession;
//		PHPUnit_Framework_Assert::assertTrue($data === null);
//	}

    private $mReturnedData;

    /**
     * @When /^I run method "([^"]*)" on \[UseCase\] with mocked repository with data "([^"]*)"$/
     * @param $arg1
     * @param $arg2
     */
    public function iRunMethodOnUseCaseWithMockedRepositoryWithData($arg1, $arg2)
    {

        $oRepo = \Mockery::mock(\WorkshopTest\Resource\Repo\DataRepository::class);
        $oRepo->shouldReceive('loadData')->once()->andReturn($arg2);

        $oUseCase = new \WorkshopTest\Resource\Operation\UseCaseApi();
        $this->mReturnedData = $oUseCase->{$arg1}($oRepo);

    }

    /**
     * @Then /^I get mocked value of "([^"]*)"$/
     * @param $arg1
     */
    public function iGetMockedValueOf($arg1)
    {
        PHPUnit_Framework_Assert::assertTrue($this->mReturnedData == $arg1);

    }

    /**
     * @Given /^I have global REQUEST variable set to "([^"]*)"="([^"]*)"$/
     * @param $arg1
     * @param $arg2
     */
    public function iHaveGlobalREQUESTVariableSetTo($arg1, $arg2)
    {
        $_REQUEST[$arg1] = $arg2;
    }

    /**
     * @Given /^I start session$/
     */
    public function iStartSession()
    {
        @session_start();
    }

}
