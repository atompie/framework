<?php
namespace WorkshopTest;

use AtomPie\DependencyInjection\DependencyInjector;
use AtomPie\Gui\Component\Part;
use AtomPie\Gui\Component\RecursiveInvoker\FactoryTreeInvoker;
use AtomPie\Web\Connection\Http\Url\Param;
use AtomPie\Web\Environment;
use AtomPie\Web\Connection\Http\Content;
use AtomPie\Web\Connection\Http\Header\ContentType;
use WorkshopTest\Resource\Component\MockComponent1;
use WorkshopTest\Resource\Component\MockComponent2;
use WorkshopTest\Resource\Config\Config;
use WorkshopTest\Resource\Param\MyParam1;
use WorkshopTest\Resource\Param\MyParam2;

require_once __DIR__ . '/../Config.php';
require_once __DIR__ . '/Resource/Component/MockComponent1.php';
require_once __DIR__ . '/Resource/Component/MockComponent2.php';

class ComponentMethodParamTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        Environment::destroyInstance();
        $oConfig = Config::get();
        Boot::up($oConfig);
        parent::setUp();
    }

    /**
     * Cleans up the environment runAfter running a test.
     */
    protected function tearDown()
    {
        Environment::destroyInstance();
        parent::tearDown();
    }

    public function testParam_NotInRequest()
    {
        $this->setExpectedException(Param\ParamException::class);
        $oComponent = new MockComponent1(Part::TOP_COMPONENT_NAME);
        $oInvoker = new FactoryTreeInvoker();
        $oInvoker->invokeFactoryForComponent(
            Boot::getComponentDi(),
            $oComponent
        );
    }

    public function testParam_NotInRequest_ButOptional()
    {
        $oComponent = new MockComponent2(Part::TOP_COMPONENT_NAME);
        $oInvoker = new FactoryTreeInvoker();
        $oInvoker->invokeFactoryForComponent(
            Boot::getComponentDi(),
            $oComponent
        );
    }

    public function testParam_InRequest()
    {

        $oRequest = Boot::getEnv()->getRequest();
        $oRequest->removeParam(MyParam1::Type()->getName());
        $oRequest->setParam(MyParam1::Type()->getName(), 1);

        $oComponent = new MockComponent1(Part::TOP_COMPONENT_NAME);
        $oInvoker = new FactoryTreeInvoker();
        $oInvoker->invokeFactoryForComponent(
            Boot::getComponentDi(),
            $oComponent
        );

        $this->assertTrue($oComponent->oMyParam1->getValue() == '1');
        $this->assertTrue($oComponent->oMyParam1->getName() == MyParam1::Type()->getName());
        $this->assertTrue($oComponent->oMyParam2->isNull());
    }

    public function testArrayParam_InRequest()
    {

        $oRequest = Boot::getEnv()->getRequest();
        $oRequest->removeParam(MyParam1::Type()->getName());
        $oRequest->removeParam(MyParam2::Type()->getName());
        $oRequest->setParam(MyParam1::Type()->getName(), array('a' => 1, 'b' => 2));
        $oRequest->setParam(MyParam2::Type()->getName(), 1);

        $oComponent = new MockComponent1(Part::TOP_COMPONENT_NAME);
        $oInvoker = new FactoryTreeInvoker();
        $oInvoker->invokeFactoryForComponent(Boot::getComponentDi(), $oComponent);

        $this->assertTrue($oComponent->oMyParam1->isArray());
        $aArray = $oComponent->oMyParam1->getValue();
        $this->assertTrue($aArray['a'] == '1');
        $this->assertTrue($oComponent->oMyParam2->getValue() == 1);

        $oRequest->removeParam(MyParam1::Type()->getName());
        $oRequest->removeParam(MyParam2::Type()->getName());

    }

    public function testNotAllowedParam_EmptyRequest()
    {

        $oRequest = Boot::getEnv()->getRequest();
        $oRequest->removeParam(MyParam1::Type()->getName());
        $oRequest->removeParam(MyParam2::Type()->getName());
        $oRequest->setParam(MyParam1::Type()->getName(), array('a' => 1, 'b' => 2));
        $oRequest->setParam(MyParam2::Type()->getName(), 1);

        $oComponent = new MockComponent1(Part::TOP_COMPONENT_NAME);
        $oInvoker = new FactoryTreeInvoker();
        $oInvoker->invokeFactoryForComponent(
            Boot::getComponentDi(),
            $oComponent
        );

        $this->setExpectedException(Param\ParamException::class);
        $oDependencyInjector = new DependencyInjector(Boot::getEndPointDi());
        $oDependencyInjector->invokeMethod($oComponent, 'test1');

    }

    public function testJsonParam()
    {

        $oEnv = Boot::getEnv();
        $oRequest = $oEnv->getRequest();
        $oRequest->removeParam(MyParam1::Type()->getName());
        $oRequest->removeParam(MyParam2::Type()->getName());

        $oRequest->setContent(
            new Content(
                json_encode(
                    array(
                        MyParam1::Type()->getName() => 1,
                    )
                ),
                new ContentType(ContentType::JSON)
            )
        );
        $oRequest->load();

        $oConfig = Config::get();
        Boot::upRequest($oConfig, $oRequest);

        $oComponent = new MockComponent1(Part::TOP_COMPONENT_NAME);
        $oInvoker = new FactoryTreeInvoker();
        $oInvoker->invokeFactoryForComponent(
            Boot::getComponentDi(),
            $oComponent
        );

        $this->assertTrue($oComponent->oMyParam1->getValue() == 1);
        $this->assertTrue($oComponent->oMyParam2->isNull());
    }

    public function testJsonParam_ArrayParam()
    {

        $oRequest = Boot::getEnv()->getRequest();
        $oRequest->removeParam('MyParam1');
        $oRequest->removeParam('MyParam2');
        $oRequest->setContent(
            new Content(
                json_encode(
                    array(
                        'MyParam1' => array('a' => 1, 'b' => '2'),
                        'MyParam2' => 2,
                    )
                ),
                new ContentType(ContentType::JSON)
            )
        );

        $oComponent = new MockComponent1(Part::TOP_COMPONENT_NAME);
        $oInvoker = new FactoryTreeInvoker();
        $oInvoker->invokeFactoryForComponent(
            Boot::getComponentDi(),
            $oComponent
        );

        $aArray = $oComponent->oMyParam1->getValue();

        $this->assertTrue($aArray['a'] == 1);
        $this->assertTrue($aArray['b'] == 2);
        $this->assertTrue($oComponent->oMyParam2->getValue() == 2);
    }

}
