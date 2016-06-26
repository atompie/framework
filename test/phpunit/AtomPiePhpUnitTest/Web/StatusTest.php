<?php
namespace AtomPiePhpUnitTest\Web;

use AtomPie\Web\Connection\Http\Header;
use AtomPie\Web\Connection\Http\Header\Status;

class StatusTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function shouldHaveStatusValueAndName()
    {
        $oStatus = new Status(200);
        $this->assertTrue($oStatus->getValue() == 200);
        $this->assertTrue($oStatus->getName() == Header::STATUS);
    }

    /**
     * @test
     */
    public function shouldChangeVersionAndMessage()
    {
        $oStatus = new Status(Status::ACCEPTED, 'Accepted String', 'HTTP/1.0');
        $this->assertTrue($oStatus->is(Status::ACCEPTED));
        $this->assertTrue($oStatus->getMessage() == 'Accepted String');
        $this->assertTrue($oStatus->getValue() == Status::ACCEPTED);
        $this->assertTrue($oStatus->getName() == 'HTTP/1.0');
        $this->assertTrue($oStatus->getVersion() == 'HTTP/1.0');
    }

}
