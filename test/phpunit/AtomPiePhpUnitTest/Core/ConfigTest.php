<?php
namespace AtomPiePhpUnitTest\Core;

use AtomPie\Core\ApplicationConfig;
use AtomPie\Core\Config\Exception;
use AtomPie\Boundary\System\IAmEnvVariable;
use AtomPie\System\Environment\EnvVariable;

/**
 * @package AtomPiePhpUnitTest\Core
 * @property string $a
 * @property string $c
 * @property null $null
 */
class BaseConfig extends ApplicationConfig
{
    public function __construct(IAmEnvVariable $oEnv)
    {
        $this->set('a', 11);
        $this->set('c', 10);
        $this->set('null', null);
    }
}

/**
 * @package AtomPiePhpUnitTest\Core
 * @property string $a
 * @property string $b
 * @property string $c
 */
class ChildOfBaseConfig extends BaseConfig
{
    public function __construct(IAmEnvVariable $oEnv)
    {
        parent::__construct($oEnv);
        $this->set('b', 12);
        $this->set('a', 13);
    }
}

class OverrideOfBaseConfig extends BaseConfig
{
    public function __construct(IAmEnvVariable $oEnv)
    {
        parent::__construct($oEnv);
        $this->override('a', 13);
        $this->override('b', 12);
    }
}


class ConfigTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function shouldReadAppConfig() {
        $oConfig = new \AtomPiePhpUnitTest\ApplicationConfig(EnvVariable::getInstance());
        $this->assertEquals('yes',$oConfig->testKey);
    }

    /**
     * @test
     */
    public function shouldInheritValues()
    {
        $childOfBaseConfig = new ChildOfBaseConfig(EnvVariable::getInstance());
        $this->assertEquals(13, $childOfBaseConfig->a);
        $this->assertEquals(12, $childOfBaseConfig->b);
        $this->assertEquals(10, $childOfBaseConfig->c);

        $childOfBaseConfig = new BaseConfig(EnvVariable::getInstance());
        $this->assertEquals(11, $childOfBaseConfig->a);
        $this->assertEquals(10, $childOfBaseConfig->c);
    }

    /**
     * @test
     */
    public function shouldBeRedOnlyAndThrowExceptionIfTryingToChange()
    {
        $oConfig = new ChildOfBaseConfig(EnvVariable::getInstance());
        $this->expectException(Exception::class);
        $oConfig->a = 1;
    }

    /**
     * @test
     */
    public function shouldThrowExceptionOnNoneExistentKey()
    {
        $oConfig = new BaseConfig(EnvVariable::getInstance());
        $this->expectException(Exception::class);
        $oConfig->noneExistent;
    }

    /**
     * @test
     */ 
    public function shouldNotThrowExceptionOnNullValues()
    {
        $oConfig = new BaseConfig(EnvVariable::getInstance());
        $this->assertNull($oConfig->null);
    }

    /**
     * @test
     */
    public function shouldThrowExceptionIfValueNotOverrode()
    {
        $this->expectException(Exception::class);
        new OverrideOfBaseConfig(EnvVariable::getInstance());
    }
}