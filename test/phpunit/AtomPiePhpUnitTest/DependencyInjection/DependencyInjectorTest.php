<?php
namespace AtomPiePhpUnitTest\DependencyInjection;

use AtomPie\Boundary\Core\IAmFrameworkConfig;
use AtomPie\Core\Dispatch\DispatchManifest;
use AtomPie\Core\Dispatch\EndPointImmutable;
use AtomPie\Core\FrameworkConfig;
use AtomPie\DependencyInjection\Dependency;
use AtomPie\DependencyInjection\DependencyInjector;
use AtomPie\DependencyInjection\Exception;
use AtomPie\DependencyInjection\Exception as InjectionException;
use AtomPie\System\DependencyContainer\EndPointDependencyContainer;
use AtomPie\System\EndPointConfig;
use AtomPie\System\Namespaces;
use AtomPie\System\UrlProvider;
use AtomPie\Web\Environment;
use AtomPiePhpUnitTest\ApplicationConfigSwitcher;

/**
 * This is EndPoint
 *
 * Class B0
 * @package AtomPiePhpUnitTest\DependencyInjection
 */
class B0
{

    /**
     * @param null $id
     * @param B1 $b1
     * @param Environment $oEnv
     * @return B1
     */
    public function test(
        /** @noinspection PhpUnusedParameterInspection */
        $id = null,
        B1 $b1,
        Environment $oEnv
    ) {
        return $b1;
    }

    public function test1(
        /** @noinspection PhpUnusedParameterInspection */
        $id = null,
        ChildOfB1 $b1,
        Environment $oEnv
    ) {
        return $b1->getConfig();
    }

    public function test2(
        /** @noinspection PhpUnusedParameterInspection */
        $id = null,
        B1 $b1,
        Environment $oEnv
    ) {
        return $b1->getConfig();
    }
}

/**
 * This is EndPoint
 *
 * Class B1
 * @package AtomPiePhpUnitTest\DependencyInjection
 */
class B1
{
    /**
     * @var IAmFrameworkConfig
     */
    private $oConfig;

    public function __construct(IAmFrameworkConfig $oConfig)
    {
        $this->oConfig = $oConfig;
    }

    /**
     * @return IAmFrameworkConfig
     */
    public function getConfig()
    {
        return $this->oConfig;
    }
}

/**
 * This is EndPoint
 *
 * Class ChildOfB1
 * @package AtomPiePhpUnitTest\DependencyInjection
 */
class ChildOfB1 extends B1
{
    /**
     * @var Environment
     */
    private $oEnv;

    public function __construct(IAmFrameworkConfig $oConfig, Environment $oEnv)
    {
        parent::__construct($oConfig);
        $this->oEnv = $oEnv;
    }

    /**
     * @return Environment
     */
    public function getEnv()
    {
        return $this->oEnv;
    }
}

class ChildOfB0 extends B0
{

}

class C0
{

    static function __build()
    {
        return new C0;
    }

    static function __constrainBuild()
    {
        return [
            AllowedEndPointForBuild::class
        ];
    }

}

class AllowedEndPointForBuild
{
    public function inject(C0 $c)
    {

    }
}

class NotAllowedEndPointForBuild
{
    public function inject(C0 $c)
    {

    }
}

class DependencyInjectorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function shouldThrowExceptionWhenInjectionOutsideConstrainedClass()
    {
        $oInjector = new DependencyInjector($this->getContainer());
        $this->expectException(InjectionException::class);
        $oInjector->invokeMethod(new NotAllowedEndPointForBuild, 'inject');
    }

    /**
     * @test
     */
    public function shouldInjectionBuilderWhenInsideConstrainedClass()
    {
        $oInjector = new DependencyInjector($this->getContainer());
        $oInjector->invokeMethod(new AllowedEndPointForBuild, 'inject');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenInjectionDoesNotMatchParamType()
    {

        $oContainer = $this->getContainer();

        $oDependency = new Dependency();
        $oDependency->setDependency(
            [
                // Here is the ERROR
                // Here we define ChildOdB1 as injection type and return B1 instead
                // Param type is set to be B1 in test1 method and B1 is not ChildOfB1
                ChildOfB1::class => function (IAmFrameworkConfig $oConfig, Environment $oEnv) {
                    return new ChildOfB1($oConfig, $oEnv);
                }
            ]
        );

        $oContainer->addDependency(
            B0::class, // Class to allow dependency
            'test2', // Method to allow dependency
            $oDependency
        );

        $oInjector = new DependencyInjector($oContainer);
        $this->expectException(Exception::class);
        $oInjector->invokeMethod(new B0(), 'test2');

    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenChildClassIsInInjectionDefinition()
    {

        $oContainer = $this->getContainer();

        $oDependency = new Dependency();
        $oDependency->setDependency(
            [
                // Here is the ERROR
                // Here we define ChildOdB1 as injection type
                // But we return B1 instead
                // Param type is set to be ChildOdB1 in test1 method
                ChildOfB1::class => function (IAmFrameworkConfig $oConfig) {
                    return new B1($oConfig);
                }
            ]
        );

        $oContainer->addDependency(
            ChildOfB0::class, // Class to allow dependency
            'test1', // Method to allow dependency
            $oDependency
        );


        $oInjector = new DependencyInjector($oContainer);
        if (class_exists(\TypeError::class)) {
            $this->expectException(\TypeError::class);
        } else {
            $this->expectException(\PHPUnit_Framework_Error::class);
        }
        $oInjector->invokeMethod(new ChildOfB0(), 'test1');
        $this->assertTrue(false);

    }

    /**
     * @test
     */
    public function shouldInjectDependencyInDependencyInChildClass()
    {

        $oContainer = $this->getContainer();

        $oDependency = new Dependency();
        $oDependency->setDependency(
            [
                B1::class => function (IAmFrameworkConfig $oConfig, Environment $oEnv) {
                    return new ChildOfB1($oConfig, $oEnv);
                }
            ]
        );

        $oContainer->addDependency(
            ChildOfB0::class, // Class to allow dependency
            'test', // Method to allow dependency
            $oDependency
        );

        $oInjector = new DependencyInjector($oContainer);
        /**
         * @var $oResult ChildOfB1
         */
        $oResult = $oInjector->invokeMethod(new ChildOfB0(), 'test');
        $this->assertEquals('none', $oResult->getConfig()->getDefaultEndPoint());
        $this->assertInstanceOf(Environment::class, $oResult->getEnv());

    }

    /**
     * @test
     */
    public function shouldInjectDependencyInDependency()
    {

        $oContainer = $this->getContainer();

        $oDependency = new Dependency();
        $oDependency->setDependency(
            [
                B1::class => function (IAmFrameworkConfig $oConfig) {
                    return new B1($oConfig);
                }
            ]
        );

        $oContainer->addDependency(
            B0::class,
            'test',
            $oDependency
        );

        $oInjector = new DependencyInjector($oContainer);
        $oResult = $oInjector->invokeMethod(new B0(), 'test');
        $this->assertEquals('none', $oResult->getConfig()->getDefaultEndPoint());

    }

    /**
     * @return EndPointDependencyContainer
     */
    private function getContainer()
    {
        $oEnvironment = Environment::getInstance();

        $oConfig = new FrameworkConfig(
            'none',
            new EndPointConfig(
                new Namespaces(),
                new Namespaces([
                    '\AtomPieTestAssets\Resource\Mock\MockEndPoint'
                ])
            ),
            new ApplicationConfigSwitcher($oEnvironment->getEnv()),
            $oEnvironment
        );
        $oManifest = new DispatchManifest($oConfig, new EndPointImmutable('none.none'));

        $oContainer = new EndPointDependencyContainer(
            Environment::getInstance(),
            $oConfig,
            $oManifest,
            new UrlProvider($oManifest, [])
        );
        return $oContainer;
    }
}
