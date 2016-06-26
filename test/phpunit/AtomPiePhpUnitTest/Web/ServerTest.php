<?php
namespace AtomPiePhpUnitTest\Web;

use AtomPie\Web\Server;

class ServerTest extends \PHPUnit_Framework_TestCase
{

    private $sBackUp;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->sBackUp = $_SERVER;

    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        parent::tearDown();
        $_SERVER = $this->sBackUp;
    }

    public function testServerPhpFile()
    {

        $_SERVER['PHP_SELF'] = '/Resource/index.php';

        $oServer = Server::getInstance(true);
        $this->assertTrue('/Resource/index.php' == $oServer->getSelfPhpFile());
        $this->assertTrue('/Resource/' == $oServer->getSelfPhpFolder());


        $_SERVER['PHP_SELF'] = '/index.php';
        $oServer = Server::getInstance(true);

        $this->assertTrue('/index.php' == $oServer->getSelfPhpFile());
        $this->assertTrue('/' == $oServer->getSelfPhpFolder());
    }

    public function testServer()
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

        $oServer = Server::getInstance(true);
        $this->assertTrue('localhost' == $oServer->getHost());
        $this->assertTrue('127.0.0.1' == $oServer->getIp());
        $this->assertTrue($oServer->isLocalHost());
        $this->assertTrue('/var/log' == $oServer->getDocumentRoot());
        $this->assertTrue('/var/www' == $oServer->getContextDocumentRoot());
        $this->assertTrue('/var/www/i.php' == $oServer->getScriptFileName());
        $this->assertTrue('8080' == $oServer->getPort());
        $this->assertTrue('34532' == $oServer->getRemotePort());
        $this->assertTrue('/Folder/i.php?XDEBUG_SESSION_START=qwerty' == $oServer->getRequestUri());
        $this->assertTrue('http://localhost:8080/Folder/i.php?XDEBUG_SESSION_START=qwerty' == $oServer->getServerUri());
        $this->assertTrue('http://localhost:8080/Folder/i.php' == $oServer->getServerUrl());
        $this->assertTrue('http://localhost:8080/Folder/' == $oServer->getServerPhpFolder());
        $this->assertTrue('/Folder/' == $oServer->getSelfPhpFolder());
        $this->assertTrue('HTTP/1.1' == $oServer->getProtocol());
        $this->assertTrue('XDEBUG_SESSION_START=qwerty' == $oServer->getQueryString());
    }

}