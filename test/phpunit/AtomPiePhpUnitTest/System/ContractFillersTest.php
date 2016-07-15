<?php
namespace AtomPiePhpUnitTest\System;

use AtomPie\System\Contract;
use AtomPie\System\ContractFillers AS Fillers;

interface ContractFillerInterface {

}

class ContractFillersTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldDeliverContractFillerWithInheritance() {

        // No fall back to class

        $oContractFillers = new Fillers(
            [
                (new Contract(ContractFillerInterface::class))
                    ->fillBy('global')

                , (new Contract(ContractFillerInterface::class))
                    ->forClass(self::class)
                    ->fillBy('class')

                , (new Contract(ContractFillerInterface::class))
                    ->forClassAndMethod(self::class, 'test')
                    ->fillBy('classAndMethod')
            ]
        );

        $oContractFiller = $oContractFillers->getContractFillerFor(
            ContractFillerInterface::class
            , self::class
            , 'test'
        );

        $this->assertEquals('classAndMethod', $oContractFiller);

        // Fall back to class

        $oContractFillers = new Fillers(
            [
                (new Contract(ContractFillerInterface::class))
                    ->fillBy('global')

                , (new Contract(ContractFillerInterface::class))
                ->forClass(self::class)
                ->fillBy('class')

            ]
        );

        $oContractFiller = $oContractFillers->getContractFillerFor(
            ContractFillerInterface::class
            , self::class
            , 'test'
        );

        $this->assertEquals('class', $oContractFiller);

        // Fall back to global

        $oContractFillers = new Fillers(
            [
                (new Contract(ContractFillerInterface::class))
                    ->fillBy('global')

            ]
        );

        $oContractFiller = $oContractFillers->getContractFillerFor(
            ContractFillerInterface::class
            , self::class
            , 'test'
        );

        $this->assertEquals('global', $oContractFiller);
    }
}
