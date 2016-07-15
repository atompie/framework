<?php
namespace AtomPiePhpUnitTest\System;

use AtomPie\System\Contract;

interface ContractInterface
{

}

class ContractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldConstrainLocalContract()
    {
        $oContract = new Contract(ContractInterface::class);
        $oContract->forClassAndMethod(self::class, 'test');
        $this->assertTrue($oContract->isForContract(ContractInterface::class));
        $this->assertTrue($oContract->isConstrainedToClass(self::class));
        $this->assertFalse($oContract->isConstrainedToClass(\PHPUnit_Framework_TestCase::class));
        $this->assertTrue($oContract->isConstrainedToClassAndMethod(self::class, 'test'));
        $this->assertFalse($oContract->isConstrainedToClassAndMethod(self::class, 'INVALID'));
        $this->assertFalse($oContract->isGlobal());
    }

    /**
     * @test
     */
    public function shouldNotConstrainGlobalContract()
    {
        $oContract = new Contract(ContractInterface::class);
        $this->assertTrue($oContract->isForContract(ContractInterface::class));
        $this->assertFalse($oContract->isConstrainedToClass(self::class));
        $this->assertFalse($oContract->isConstrainedToClass(\PHPUnit_Framework_TestCase::class));
        $this->assertFalse($oContract->isConstrainedToClassAndMethod(self::class, 'test'));
        $this->assertFalse($oContract->isConstrainedToClassAndMethod(self::class, 'INVALID'));
        $this->assertTrue($oContract->isGlobal());
    }

    /**
     * @test
     */
    public function shouldDeliverContract()
    {
        $oContract = new Contract(ContractInterface::class);
        $oContract->fillBy(self::class);
        $this->assertEquals(self::class, $oContract->getContractFiller());
    }
}
