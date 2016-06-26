<?php
namespace AtomPiePhpUnitTest\Core;

use AtomPie\Core\Dispatch\EndPointUrl;

class EndPointUrlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReturnUrlString()
    {
        $oEndPointUrl = new EndPointUrl('Object.Method', ['param' => 'value']);
        $this->assertTrue($oEndPointUrl->__toString() == 'Object.Method?param=value');
    }
}
