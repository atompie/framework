<?php
namespace AtomPiePhpUnitTest\Web;

use AtomPie\Web\Connection\Http\Url;

class UrlTest extends \PHPUnit_Framework_TestCase
{

    public function testImmutableUrl()
    {
        $oUrl = new Url('url', array('id' => 1));
        $oUrl->setAnchor('anchor');
        $this->assertTrue($oUrl->__toString() == 'url?id=1#anchor');
        $this->assertTrue($oUrl->hasAnchor());
        $this->assertTrue($oUrl->getAnchor() == 'anchor');
        $this->assertTrue($oUrl->getParamsAsString(array('id' => 1)) == 'id=1');
        $this->assertTrue($oUrl->getRequestString() == 'id=1');
        $this->assertTrue($oUrl->hasParams());
        $this->assertTrue($oUrl->getUrl() == 'url');
        $this->assertTrue($oUrl->getParam('id') == '1');
        $aParams = $oUrl->getParams();
        $this->assertTrue($aParams['id'] == '1');
        $this->assertTrue(count($aParams) == 1);
    }

}
