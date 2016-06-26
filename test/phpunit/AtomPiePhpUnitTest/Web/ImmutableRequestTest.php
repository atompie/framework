<?php
namespace AtomPiePhpUnitTest\Web;

use AtomPie\Web\Connection\Http\Header;
use AtomPie\Web\Connection\Http\Header\Accept;
use AtomPie\Web\Connection\Http\Header\Authorization;
use AtomPie\Web\Connection\Http\ImmutableRequest;
use AtomPie\Web\Connection\Http\Request;

@session_start();

class ImmutableRequestTest extends \PHPUnit_Framework_TestCase
{

    public function testLoadingRequestParams()
    {

        $_GET = array();
        $_POST = array();
        $_REQUEST = array();
        $_REQUEST['Param1'] = '1';
        $_REQUEST['Param2'] = '2';

        $oRequest = new ImmutableRequest();
        $oRequest->load();

        $this->assertTrue($oRequest->getParam('Param1') === '1');
        $this->assertTrue($oRequest->getParam('Param2') === '2');

        $this->assertFalse($oRequest->hasParam('Param1', Request\Method::POST));
        $this->assertFalse($oRequest->hasParam('Param2', Request\Method::POST));

        $this->assertFalse($oRequest->hasParam('Param1', Request\Method::GET));
        $this->assertFalse($oRequest->hasParam('Param2', Request\Method::GET));
    }

    public function testLoadingPostParams()
    {

        $_GET = array();
        $_POST = array();
        $_POST['Param1'] = '1';
        $_POST['Param2'] = '2';

        $oRequest = new ImmutableRequest();
        $oRequest->load();

        $this->assertTrue($oRequest->getParam('Param1') === '1');
        $this->assertTrue($oRequest->getParam('Param2') === '2');

        $this->assertTrue($oRequest->getParam('Param1', Request\Method::POST) === '1');
        $this->assertTrue($oRequest->getParam('Param2', Request\Method::POST) === '2');

        $this->assertFalse($oRequest->hasParam('Param1', Request\Method::GET));
        $this->assertFalse($oRequest->hasParam('Param2', Request\Method::GET));
    }

    public function testLoadingGetAndPostParams()
    {

        $_POST = array();
        $_POST['Param1'] = '1';
        $_POST['Param2'] = '2';
        $_GET = array();
        $_GET['Param3'] = '3';
        $_GET['Param4'] = '4';

        $oRequest = new ImmutableRequest();
        $oRequest->load();
        $this->assertTrue($oRequest->getParam('Param1') === '1');
        $this->assertTrue($oRequest->getParam('Param2') === '2');
        $this->assertTrue($oRequest->getParam('Param1', Request\Method::POST) === '1');
        $this->assertTrue($oRequest->getParam('Param2', Request\Method::POST) === '2');
        $this->assertTrue($oRequest->getParam('Param3', Request\Method::GET) === '3');
        $this->assertTrue($oRequest->getParam('Param4', Request\Method::GET) === '4');

        // Do not mix post and get
        $this->assertFalse($oRequest->hasParam('Param1', Request\Method::GET));
        $this->assertFalse($oRequest->hasParam('Param2', Request\Method::GET));
        $this->assertFalse($oRequest->hasParam('Param3', Request\Method::POST));
        $this->assertFalse($oRequest->hasParam('Param4', Request\Method::POST));

    }

    public function testLoadingAccept()
    {

        $_SERVER['HTTP_AUTHORIZATION'] = 'Basic ASz*d=';
        $_SERVER['HTTP_ACCEPT'] = 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';

        $oRequest = new ImmutableRequest();
        $oRequest->load();

        $this->assertTrue($oRequest->getHeader(Header::AUTHORIZATION) instanceof Authorization);
        /** @var Authorization $oAuth */
        $oAuth = $oRequest->getHeader(Header::AUTHORIZATION);
        $this->assertTrue($oAuth->isAuth('basic'));
        $this->assertTrue($oAuth->getAuthenticationType() == 'Basic');
        $this->assertTrue($oAuth->getToken() == 'ASz*d=');

        $this->assertTrue($oRequest->getHeader(Header::ACCEPT) instanceof Accept);
    }

    public function testServerEnv()
    {
        $_SERVER['REQUEST_METHOD'] = 'post';
        $_SERVER['REMOTE_ADDR'] = '1.1.1.1';
        $_SERVER['QUERY_STRING'] = 'index.php';
        $_SERVER["CONTENT_TYPE"] = 'application/xml';

        $oRequest = new ImmutableRequest();
        $oRequest->load();

        $this->assertTrue($oRequest->getMethod() == $_SERVER['REQUEST_METHOD']);
        $this->assertTrue($oRequest->getRemoteAddress() == $_SERVER['REMOTE_ADDR']);
        $this->assertTrue($oRequest->getRequestString() == $_SERVER['QUERY_STRING']);
        $this->assertTrue($oRequest->getContent()->getContentType()->getValue() == $_SERVER['CONTENT_TYPE']);
    }

    public function testSession()
    {
//		$_SESSION = array();
        $_SESSION['test'] = '1';
        var_dump($_SESSION);
        $oRequest = new Request();
        $oResponse = $oRequest->send('http://www.onet.pl');
        $_SESSION['test1'] = '2';
//		echo $oResponse->getContent()->get();
        var_dump($_SESSION);
    }

    public function testSession1()
    {
        var_dump($_SESSION);
    }
}
