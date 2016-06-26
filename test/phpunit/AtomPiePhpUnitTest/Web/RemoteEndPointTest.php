<?php
namespace AtomPiePhpUnitTest\Web;

use AtomPie\Web\RemoteEndPoint;

require_once __DIR__ . '/Mock/MyParam1.php';

class RemappedObject
{
    public $a;
}

class RemoteEndPointTest extends \PHPUnit_Framework_TestCase
{

    const REMOTE_END_POINT_URL = 'http://192.168.100.100/php.example/src/Ex/Resource';

    public function testRemoteService()
    {
        $this->getDataAction(new Mock\MyParam1("pass"));
    }

    private function getDataAction($a)
    {
        $oServ = new RemoteEndPoint(self::REMOTE_END_POINT_URL, 'Api\Data');
        return $oServ->call(__FUNCTION__, func_get_args());
    }

}