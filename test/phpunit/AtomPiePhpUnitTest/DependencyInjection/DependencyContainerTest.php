<?php
namespace AtomPiePhpUnitTest\DependencyInjection;

use AtomPie\Boundary\Core\IAmFrameworkConfig;
use AtomPie\Core\Dispatch\DispatchManifest;
use AtomPie\Core\Dispatch\EndPointImmutable;
use AtomPie\Core\FrameworkConfig;
use AtomPie\DependencyInjection\Boundary\IAmDependencyMetaData;
use AtomPie\DependencyInjection\Dependency;
use AtomPie\DependencyInjection\DependencyContainer;
use AtomPie\DependencyInjection\DependencyInjector;
use AtomPie\DependencyInjection\Exception;
use AtomPie\System\DependencyContainer\EndPointDependencyContainer;
use AtomPie\System\Router;
use AtomPie\System\UrlProvider;
use AtomPie\Web\Environment;
use AtomPiePhpUnitTest\ApplicationConfigDefinition;

interface Interface1
{
}

interface Interface2
{
}

class A1
{
    public function test() {

    }
}

class A2 implements Interface1
{

}

class A3 extends A1
{

}

class EndPoint1
{

    function typeLess($a)
    {
    }

    function injection(Interface1 $i1, A1 $o1)
    {
        return $i1 instanceof A2 and $o1 instanceof A1;
    }

    static function staticInjection(Interface1 $i1, A3 $o1)
    {
        return $i1 instanceof A2 and $o1 instanceof A3;
    }
}

class EndPoint2 extends EndPoint1
{

}

class DependencyContainerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     * @throws Exception
     */
    public function shouldInjectTypeLessParamIntoCustomDi()
    {

        $oContainer1 = new DependencyContainer();
        $oContainer1->forFunction('typeLess')->setDependency(
            []
        );

        $oPhpUnit = $this;

        // Invoke closure

        $oInjector = new DependencyInjector($oContainer1);
        $oInjector->invokeClosure('typeLess', function ($a) use ($oPhpUnit) {
                $oPhpUnit->assertTrue($a == 'test1');
            }
            , [
                Dependency::TYPE_LESS => function () {
                    return 'test1';
                },
            ]
        );
    }

    /**
     * @test
     * @throws Exception
     */
    public function shouldInjectTypeLessParam()
    {

        $oContainer1 = new DependencyContainer();
        $oContainer1->forFunction('injection')->setDependency(
            array(
                Dependency::TYPE_LESS => function (IAmDependencyMetaData $oMeta) {
                    return $oMeta->getParamMetaData()->getName();
                },
                Interface1::class => function () {
                    return new A2;
                },
            )
        );

        $oPhpUnit = $this;

        $oInjector = new DependencyInjector($oContainer1);
        $oInjector->invokeClosure('injection', function ($i1) use ($oPhpUnit) {
            $oPhpUnit->assertTrue($i1 == 'i1');
        });
    }

    /**
     * @test
     */
    public function shouldInjectClosureDependenciesAndKeepTypeLessDependencies()
    {
        $oEnvironment = Environment::getInstance();
            
        $oConfig = new FrameworkConfig(
            $oEnvironment,
            new Router(__DIR__.'/../../AtomPieTestAssets/Routing/Routing.php'),
            new ApplicationConfigDefinition($oEnvironment->getEnv()),
            __DIR__,
            __DIR__ . '/../AtomPieTestAssets/Resource/Theme',
            [],
            [],
            [
                '\AtomPieTestAssets\Resource\Mock\MockEndPoint'
            ],
            [],
            'none'
        );
        $oManifest = new DispatchManifest($oConfig, new EndPointImmutable('none.none'));

        // It has typeless dependencies
        $oContainer = new EndPointDependencyContainer(
            Environment::getInstance(),
            $oConfig,
            $oManifest,
            new UrlProvider($oManifest,[])
        );

        // Add dependency for closure
        $oContainer->forFunction('closure')->addDependency([
            A2::class => function (IAmFrameworkConfig $oConfig) {
                return new A2($oConfig);
            }
        ]);

        // It has typeless dependencies too
        $oDependency = $oContainer->getInjectionClosureFor(Dependency::CLOSURE,'test');
        $this->assertTrue($oDependency->hasTypeLessDependency());
        $this->assertTrue($oDependency->hasDependency(A2::class));
    }

    /**
     * @test
     */
    public function shouldInjectTypeLessParamEvenIfThereIsDependencyForGivenClass()
    {

        $oEnvironment = Environment::getInstance();
        
        $oConfig = new FrameworkConfig(
            $oEnvironment,
            new Router(__DIR__.'/../../AtomPieTestAssets/Routing/Routing.php'),
            new ApplicationConfigDefinition($oEnvironment->getEnv()),
            __DIR__,
            __DIR__ . '/../AtomPieTestAssets/Resource/Theme',
            [],
            [],
            [
                '\AtomPieTestAssets\Resource\Mock\MockEndPoint'
            ],
            [],
            'none'
        );
        $oManifest = new DispatchManifest($oConfig, new EndPointImmutable('none.none'));

        $oContainer = new EndPointDependencyContainer(
            Environment::getInstance(),
            $oConfig,
            $oManifest,
            new UrlProvider($oManifest,[])
        );

        $oDependency = new Dependency();
        $oDependency->setDependency(
            [
                A2::class => function (IAmFrameworkConfig $oConfig) {
                    return new A2($oConfig);
                }
            ]
        );

        $oContainer->addDependency(
            A1::class,
            'test',
            $oDependency
        );
        $oDependency = $oContainer->getInjectionClosureFor(A1::class,'test');
        $this->assertTrue($oDependency->hasTypeLessDependency());
        $this->assertTrue($oDependency->hasDependency(A2::class));
    }

    /**
     * @test
     * @throws Exception
     */
    public function shouldHaveDefinitionOfClassByInterface()
    {

        $oContainer = new DependencyContainer();
        $oContainer->forMethod('Class', 'Method')->setDependency(
            array(
                Interface1::class => function () {
                    return 1;
                },
                Interface2::class => function () {
                    return 2;
                }
            )
        );
        $this->assertTrue($oContainer->hasDependency('Class', 'Method', Interface1::class));
        $this->assertTrue($oContainer->hasDependency('Class', 'Method', Interface2::class));
        $this->assertFalse($oContainer->hasDependency('Class', 'Method', 'Interface3'));
        $this->assertFalse($oContainer->hasDependency('Class1', 'Method1', 'Interface1'));

        $aDependencies = $oContainer->getDependencies('Class', 'Method');
        $sClosure = $aDependencies[Interface1::class];
        $this->assertTrue($sClosure() === 1);

    }

    /**
     * @test
     * @throws Exception
     */
    public function shouldInjectInterfaceToAnyClass()
    {

        $oContainer = new DependencyContainer();
        $oContainer->forAnyClass()->setDependency(
            array(
                Interface1::class => function () {
                    return 1;
                },
                Interface2::class => function () {
                    return 2;
                }
            )
        );
        $this->assertTrue($oContainer->hasDependency('Class', 'Method', Interface1::class));
        $this->assertTrue($oContainer->hasDependency('Class', 'Method', Interface2::class));
        $this->assertFalse($oContainer->hasDependency('Class', 'Method', 'Interface3'));
        $this->assertTrue($oContainer->hasDependency('Class1', 'Method1', Interface1::class));

        $aDependencies = $oContainer->getDependencies('Class', 'Method');
        $sClosure = $aDependencies[Interface1::class];
        $this->assertTrue($sClosure() === 1);

    }

    /**
     * @test
     * @throws Exception
     */
    public function shouldBeAbleToAddInjectionToDependencyContainer()
    {

        $oContainer = new DependencyContainer();
        $oContainer->forMethod('Class', 'Method')->setDependency(
            array(
                'Interface1' => function () {
                    return true;
                },
                'Interface2' => function () {
                    return false;
                }
            )
        );
        $this->assertTrue($oContainer->hasDependency('Class', 'Method', 'Interface1'));
        $this->assertTrue($oContainer->hasDependency('Class', 'Method', 'Interface2'));

        $oContainer->forMethod('Class', 'Method')->addDependency(
            array(
                'Interface3' => function () {
                    return 3;
                },
            )
        );

        $this->assertTrue($oContainer->hasDependency('Class', 'Method', 'Interface1'));
        $this->assertTrue($oContainer->hasDependency('Class', 'Method', 'Interface2'));
        $this->assertTrue($oContainer->hasDependency('Class', 'Method', 'Interface3'));
    }

    /**
     * Dependencies can be set only on empty container.
     * Use add instead.
     *
     * @test
     * @throws Exception
     */
    public function shouldThrowExceptionWhileReplacingNotEmptyDependencyContainer()
    {

        $oContainer = new DependencyContainer();
        $oContainer->forMethod('Class', 'Method')->setDependency(
            array(
                'Interface1' => function () {
                    return true;
                },
                'Interface2' => function () {
                    return false;
                }
            )
        );
        // Dependencies can be set only on empty container.
        $this->setExpectedException(Exception::class);
        $oContainer->forMethod('Class', 'Method')->setDependency(
            array(
                'Interface1' => function () {
                    return true;
                },
                'Interface2' => function () {
                    return false;
                }
            )
        );

    }

    /**
     * @test
     * @throws Exception
     */
    public function shouldInjectDependencyDuringMethodInvoke()
    {

        $oContainer = new DependencyContainer();
        $oContainer->forMethod(EndPoint1::class, 'injection')->setDependency(
            array(
                Interface1::class => function () {
                    return new A2;
                },
                Interface2::class => function () {
                    return new A3;
                },
                A1::class => function () {
                    return new A1;
                }
            )
        );

        $oInject = new DependencyInjector($oContainer);
        $oInject->invokeMethod(new EndPoint1, 'injection');

    }

    /**
     * @test
     * @throws Exception
     */
    public function shouldInjectDependencyDuringMethodInvokeWithCustomParam()
    {

        $oContainer = new DependencyContainer();
        $oContainer->forMethod(EndPoint1::class, 'injection')->setDependency(
            array(
                Interface1::class => function () {
                    return new A2;
                },
                Interface2::class => function () {
                    return new A3;
                },

            )
        );

        $oInject = new DependencyInjector($oContainer);
        $oInject->invokeMethod(new EndPoint1, 'injection', [
            A1::class => function () {
                return new A1;
            }
        ]);

    }

    /**
     * Injects Interface1 but its implementation returns A2 that implements Interface1
     *
     * @test
     * @throws Exception
     */
    public function shouldRecognizeObjectWithImplementedInterfaceAsValidParameter()
    {

        $oPhpUnit = $this;

        $oContainer = new DependencyContainer();
        $oContainer->forMethod(EndPoint1::class, 'injection')->setDependency(
            array(
                Interface1::class => function (IAmDependencyMetaData $oMetaData) use ($oPhpUnit) {
                    // This is the clue of this test. Checks if object was passed
                    $oObject = $oMetaData->getObject();
                    $oPhpUnit->assertTrue($oObject instanceof EndPoint1);
                    return new A2;
                },
                Interface2::class => function () {
                    return new A3;
                },
                A1::class => function () {
                    return new A1;
                }
            )
        );

        $oInject = new DependencyInjector($oContainer);
        $oInject->invokeMethod(
            new EndPoint1, // This is the object
            'injection'
        );

    }

    /**
     * Throws exception if injection is set for incorrect method or class.
     *
     * @test
     * @throws Exception
     */
    public function shouldThrowExceptionIfInjectionIsDefinedForIncorrectMethodOrClass()
    {
        $oContainer = new DependencyContainer();
        $oContainer->forMethod('Class', 'Method')->setDependency(
            array(
                Interface1::class => function () {
                    return 1;
                },
                Interface2::class => function () {
                    return 2;
                },
                A1::class => function () {
                    return 3;
                }
            )
        );
        // Could not find any DI configuration
        $this->setExpectedException(Exception::class);
        $oInject = new DependencyInjector($oContainer);
        $oInject->invokeMethod(new EndPoint1, 'injection');
    }

    /**
     * Method required object A1 which is not defined in dependency injection.
     *
     * @test
     * @throws Exception
     */
    public function shouldThrowExceptionIfNoDependencySetForGivenParam()
    {
        $oContainer = new DependencyContainer();
        $oContainer->forMethod(EndPoint1::class, 'injection')->setDependency(
            array(
                Interface1::class => function () {
                    return new A2;
                },
            )
        );
        // Could not inject class [AtomPiePhpUnitTest\DependencyInjection\A1]
        $this->setExpectedException(Exception::class);
        $oInject = new DependencyInjector($oContainer);
        $oInject->invokeMethod(new EndPoint1, 'injection');
    }

    /**
     * @test
     * @throws Exception
     */
    public function shouldInjectIntoStaticMethodAndGetItsMetaData()
    {

        $oPhpUnit = $this;

        $oContainer = new DependencyContainer();
        $oContainer->forMethod(EndPoint1::class, 'staticInjection')->setDependency(
            array(
                Interface1::class => function (IAmDependencyMetaData $oMetaData) use ($oPhpUnit) {
                    $oPhpUnit->assertTrue($oMetaData->getParamMetaData()->getClass()->getName() == Interface1::class);
                    $oPhpUnit->assertTrue($oMetaData->getParamMetaData()->getName() == 'i1');
                    return new A2;
                },
                A1::class => function (IAmDependencyMetaData $oMetaData) use ($oPhpUnit) {
                    // Method staticInjection has param A3 as type so its subtype A1 was catch but param type is A3
                    $oPhpUnit->assertTrue($oMetaData->getParamMetaData()->getClass()->getName() == A3::class);
                    $oPhpUnit->assertTrue($oMetaData->getParamMetaData()->getName() == 'o1');
                    return new A3;
                },

            )
        );
        $oInjector = new DependencyInjector($oContainer);
        $oInjector->invokeMethod(EndPoint1::class, 'staticInjection');
    }

    /**
     * See that method staticInjection has param A3 as
     * type so its subtype A1 was injected though param type is A3.
     * See that EndPoint2::class is child of EndPoint1::class.
     *
     * @test
     * @throws Exception
     */
    public function shouldInjectDescendantClassIntoStaticMethod()
    {

        $oPhpUnit = $this;

        $oContainer = new DependencyContainer();
        $oContainer->forMethod(EndPoint1::class, 'staticInjection')->setDependency(
            array(
                Interface1::class => function (IAmDependencyMetaData $oMetaData) use ($oPhpUnit) {
                    $oPhpUnit->assertTrue($oMetaData->getParamMetaData()->getClass()->getName() == Interface1::class);
                    $oPhpUnit->assertTrue($oMetaData->getParamMetaData()->getName() == 'i1');
                    return new A2;
                },
                A1::class => function (IAmDependencyMetaData $oMetaData) use ($oPhpUnit) {
                    // Method staticInjection has param A3 as type so its subtype A1 was catch but param type is A3
                    $oPhpUnit->assertTrue($oMetaData->getParamMetaData()->getClass()->getName() == A3::class);
                    $oPhpUnit->assertTrue($oMetaData->getParamMetaData()->getName() == 'o1');
                    return new A3;
                },

            )
        );
        $oInjector = new DependencyInjector($oContainer);
        // EndPoint2::class is descendant
        $oInjector->invokeMethod(EndPoint2::class, 'staticInjection');
    }

    /**
     * @test
     * @throws Exception
     */
    public function shouldInjectToMethodsAndClosures()
    {

        $oContainer = new DependencyContainer();
        $oContainer->forMethod(Dependency::TYPE_LESS, 'injection')->setDependency(
            array(
                Interface1::class => function () {
                    return new A2;
                },
                Interface2::class => function () {
                    return new A3;
                },
                A1::class => function () {
                    return new A1;
                }
            )
        );
        $oContainer->forMethod(EndPoint1::class, 'staticInjection')->addDependency(
            array(
                Interface1::class => function () {
                    return new A2;
                },
                Interface2::class => function () {
                    return new A3;
                },
                A1::class => function (IAmDependencyMetaData $oMetaData) {
                    $sType = $oMetaData->getParamMetaData()->getClass()->getName();
                    return new $sType;
                }
            )
        );

        $oContainer->forFunction('closure')->setDependency(
            array(
                Interface1::class => function () {
                    return new A2;
                },
                Interface2::class => function () {
                    return new A3;
                },
                A1::class => function () {
                    return new A1;
                }
            )
        );

        $oPhpUnit = $this;

        $oClass = new DependencyInjector($oContainer);
        $this->assertTrue($oClass->invokeMethod(new EndPoint1(), 'injection'));
        $this->assertTrue($oClass->invokeMethod(EndPoint1::class, 'staticInjection'));
        $oClass->invokeClosure('closure', function (Interface1 $o1) use ($oPhpUnit) {
            $oPhpUnit->assertTrue($o1 instanceof A2);
            return 1;
        });

    }

    /**
     * @test
     * @throws Exception
     */
    public function shouldMergeDependenciesToTheSameContainer()
    {

        $oContainer1 = new DependencyContainer();
        $oContainer1->forFunction('injection')->setDependency(
            array(
                Interface1::class => function () {
                    return new A2;
                },
            )
        );
        $oContainer2 = new DependencyContainer();
        $oContainer2->forFunction('injection')->setDependency(
            array(
                Interface2::class => function () {
                    return new A3;
                },
                A1::class => function () {
                    return new A1;
                }
            )
        );
        $oContainer2->merge($oContainer1);

        $this->assertTrue(count(array_keys($oContainer1->getDependencies(Dependency::CLOSURE, 'injection'))) == 1);
        // Now has 3 dependencies definitions.
        $this->assertTrue(count(array_keys($oContainer2->getDependencies(Dependency::CLOSURE, 'injection'))) == 3);

        $oPhpUnit = $this;

        $oInjector = new DependencyInjector($oContainer2);

        // Can invoke from both definitions

        $oInjector->invokeClosure('injection', function (Interface1 $i1) use ($oPhpUnit) {
            $oPhpUnit->assertTrue($i1 instanceof A2);
        });

        $oInjector->invokeClosure('injection', function (A1 $i1) use ($oPhpUnit) {
            $oPhpUnit->assertTrue($i1 instanceof A1);
        });
    }

    /**
     * @test
     * @throws Exception
     */
    public function shouldMergeDifferentContainersIntoOneDefinitions()
    {

        $oContainer1 = new DependencyContainer();
        $oContainer1->forFunction('container1')->setDependency(
            array(
                Interface1::class => function () {
                    return new A2;
                },
            )
        );
        $oContainer2 = new DependencyContainer();
        $oContainer2->forFunction('container2')->setDependency(
            array(
                Interface2::class => function () {
                    return new A3;
                },
                A1::class => function () {
                    return new A1;
                }
            )
        );

        $this->assertTrue(count(array_keys($oContainer1->getDependencies(Dependency::CLOSURE, 'container1'))) == 1);
        $this->assertTrue(count(array_keys($oContainer2->getDependencies(Dependency::CLOSURE, 'container2'))) == 2);

        $oContainer2->merge($oContainer1);

        $this->assertTrue(count(array_keys($oContainer2->getDependencies(Dependency::CLOSURE, 'container1'))) == 3);
        $this->assertTrue(count(array_keys($oContainer2->getDependencies(Dependency::CLOSURE, 'container2'))) == 3);

        $this->assertTrue(count(array_keys($oContainer1->getDependencies(Dependency::CLOSURE, 'container1'))) == 1);
        $this->assertTrue(count(array_keys($oContainer1->getDependencies(Dependency::CLOSURE, 'container2'))) == 1);

        $oPhpUnit = $this;

        $oInjector = new DependencyInjector($oContainer2);
        $bReturn = $oInjector->invokeClosure('container1', function (Interface1 $i1) use ($oPhpUnit) {
            $oPhpUnit->assertTrue($i1 instanceof A2);
            return true;
        });

        $this->assertTrue($bReturn);


        // Can inject class [AtomPiePhpUnitTest\DependencyInjection\A1]
        $bReturn = $oInjector->invokeClosure('container1', function (A1 $i1) use ($oPhpUnit) {
            $oPhpUnit->assertTrue($i1 instanceof A1);
            return true;
        });

        $this->assertTrue($bReturn);

        $oInjector = new DependencyInjector($oContainer1);
        $this->setExpectedException(Exception::class);
        // TODO injection into closure with wrong ClosureId does not matter
        // TODO as all closure injections are merged into one.
        // TODO you can pass any id
        // TODO therefore closure ids should be removed
        $oInjector->invokeClosure('WRONG_ID', function (A1 $c) use ($oPhpUnit) {
            $oPhpUnit->assertTrue($c instanceof A1);
            return true;
        });
    }

    /**
     * @test
     * @throws Exception
     */
    public function shouldInjectToAllMethodsWhenDefinedAsForAnyMethods()
    {

        $oContainer = new DependencyContainer();
        $oContainer->forAnyMethodInClass(EndPoint1::class)->setDependency(
            array(
                Interface1::class => function () {
                    return new A2;
                },
                Interface2::class => function () {
                    return new A3;
                },
                A1::class => function (IAmDependencyMetaData $oMeta) {
                    $sClassType = $oMeta->getParamMetaData()->getClass()->getName();
                    return new $sClassType();
                }
            )
        );

        $oClass = new DependencyInjector($oContainer);
        $this->assertTrue($oClass->invokeMethod(new EndPoint1(), 'injection'));
        $this->assertTrue($oClass->invokeMethod(EndPoint1::class, 'staticInjection'));

    }

    /**
     * @test
     * @throws Exception
     */
    public function shouldRejectInjectionIfFactoryReturnsNotCompatibleType()
    {

        $oContainer1 = new DependencyContainer();
        $oContainer1->forFunction('container1')->setDependency(
            array(
                Interface1::class => function () {
                    return new A1; // A1 does not implement Interface1
                },
            )
        );
        $oInjector = new DependencyInjector($oContainer1);

        // Could not inject class [AtomPiePhpUnitTest\DependencyInjection\A1]
        $this->setExpectedException(Exception::class);
        $oInjector->invokeClosure('container1', function (A1 $i1)  {
        });
    }


}
