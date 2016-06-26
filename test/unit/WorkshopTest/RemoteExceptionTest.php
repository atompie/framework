<?php
namespace WorkshopTest;

use AtomPie\Service\Api\RemoteException;

class RemoteExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $aExceptionTrace = array('Line1', 'Line2');
        $oException = new RemoteException('File', 1, 'Message', array('Line1', 'Line2'));
        $this->assertTrue($oException->getTrace() == $aExceptionTrace);
        $this->assertTrue($oException->getLine() == 1);
        $this->assertTrue($oException->getMessage() == 'Message');
        $this->assertTrue($oException->getFile() == 'File');
        $this->assertTrue($oException->getCode() == 0);
    }
}
