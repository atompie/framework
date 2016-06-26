<?php
namespace WorkshopTest;

use WorkshopTest\Resource\Operation\UseCaseApi;
use WorkshopTest\Resource\Repo\DataRepository;

class UseCaseApiTest extends \PHPUnit_Framework_TestCase
{
    public function testApi_Mockery()
    {
        $oUseCase = new UseCaseApi();

        $oRepo = \Mockery::mock(DataRepository::class);
        $oRepo->shouldReceive('loadData')->once()->andReturn('mock-data');
        $this->assertTrue('mock-data' == $oUseCase->getDataFromRepo($oRepo));
    }
}
