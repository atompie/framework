<?php
namespace AtomPiePhpUnitTest\Core;

use AtomPie\Core\Dispatch\EndPointImmutable;
use AtomPie\Core\Dispatch\QueryString;

class EndPointImmutableTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function shouldParseEndPointUrl()
    {

        $oEndPointSpec = new EndPointImmutable(QueryString::urlEscape('Namespace\\Class.Method'));
        $this->assertTrue($oEndPointSpec->getClassString() == 'Namespace\\Class');
        $this->assertTrue($oEndPointSpec->getMethodString() == 'Method');

    }

}
