<?php
namespace AtomPiePhpUnitTest\Web;

use AtomPie\Web\Connection\Http\Request;

class RequestTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $_REQUEST = array();
        $_GET = array();
        $_POST = array();
        parent::setUp();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        parent::tearDown();
        $_REQUEST = array();
        $_GET = array();
        $_POST = array();
    }

    public function testLoad_ReferenceToREQUEST()
    {

        $oRequest = new Request();
        $oRequest->setParam('test1', 1);
        $oRequest->setParam('test2', 2);

        $this->assertTrue($oRequest->getParam('test1') == 1);

        // No reference
        $_REQUEST['test1'] = 2;
        $_POST['test1'] = 2;
        $_GET['test1'] = 2;
        $this->assertTrue($oRequest->getParam('test1') == 1);
        $this->assertTrue($oRequest->getParam('test2') == 2);

    }

    /**
     * @test
     * @throws \Exception
     * @throws \AtomPie\Web\Exception
     */
    public function shouldReturnValidResponse()
    {
        $_SESSION = array();
        $_SESSION['test'] = '1';
        $oRequest = new Request();
        $oResponse = $oRequest->send('http://www.onet.pl');
        $this->assertTrue(strstr($oResponse->getContent()->get(), '<!DOCTYPE html>') !== false);
        $this->assertTrue($oResponse->getStatus()->isOk());
        $this->assertTrue($oResponse->getStatus()->getMessage() == 'OK');
        $this->assertTrue($oResponse->getStatus()->getVersion() == 'HTTP/1.1');
    }

//	/**
//	 * Constructs the test case.
//	 */
//	public function testProxy() {
//		$oRequest = new \AtomPie\Web\Connection\Http\Request();
//		$oRequest->setProxy('24.100.137.39:3128');
//		$oRequest->setTimeOut(3600);
//		$oResponse = $oRequest->send('http://www.onet.pl');
//
//		echo $oResponse->getContent()->get();
//	}

//	public function testMethod() {
//		$oRequest = new \AtomPie\Web\Connection\Http\Request(\AtomPie\Web\Connection\Http\Request::POST);
//		$oRequest->send((string)new \AtomPie\Web\Connection\Http\Url('xxx',array('id'=>1)));
//	}

}

