<?php
namespace AtomPiePhpUnitTest\Web;

use AtomPiePhpUnitTest\Web\Mock\MyConstrainedParamTest;
use AtomPiePhpUnitTest\Web\Mock\MyParamExample;
use AtomPie\Web\Connection\Http\Url\Param;
use AtomPie\Web\Exception;

require_once __DIR__ . '/Mock/MyParamExample.php';
require_once __DIR__ . '/Mock/MyConstrainedParamTest.php';

class ParamTest extends \PHPUnit_Framework_TestCase
{

    public function testParam_ToString()
    {
        $oParam = new Param('Param', 'text&text');
        $this->assertTrue($oParam->__toString() == 'Param=text%26text');

        $oMyParam = new MyParamExample('MyParamTest', 'text&text');
        $this->assertTrue($oMyParam->__toString() == 'MyParamTest=text%26text');

    }

    public function testParam_Constrains()
    {
        $this->expectException(Param\ParamException::class);
        new MyConstrainedParamTest('MyConstrainedParamTest', 'text&text');
    }

    public function testParam_AssigningValue()
    {

        $oParam = new Param('Param', '1');

        $this->assertTrue($oParam->getValue() == '1');
        $this->assertTrue($oParam->getName() == 'Param');

        $oParam = new Param('Param', "1Ś<a \\>");
        $this->assertTrue($oParam->getValue() == '1Ś');

    }

    public function testParam_ArrayAccess()
    {
        $oParam = new Param('Param', array('a' => 1, 'b' => 2));
        $this->assertTrue($oParam['a'] == 1);
        $this->assertTrue($oParam['b'] == 2);
        $this->assertTrue(isset($oParam['b']));
        unset($oParam['b']);
        $this->assertTrue(!isset($oParam['b']));
        $this->expectException(Exception::class);
        $oParam['c'] = 3;
        $this->assertTrue(!isset($oParam['c']));
    }

    public function testParam_ArrayAccess_NoArray()
    {
        $oParam = new Param('Param', 1);
        $this->expectException(Exception::class);
        $oParam['a'];
    }

    public function testParam_IsEmpty()
    {

        $oParam = new Param('Param', array());
        $this->assertFalse($oParam->isNull());

        $oParam = new Param('Param', '');
        $this->assertFalse($oParam->isNull());

        $oParam = new Param('Param', 0);
        $this->assertFalse($oParam->isNull());

        $oParam = new Param('Param', null);
        $this->assertTrue($oParam->isNull());

    }

    public function testParam_Array()
    {
        $oParam = new Param('Param', array('key' => 1));
        $this->assertTrue($oParam->__toString() == 'Param%5Bkey%5D=1');
    }

}
