<?php
namespace WorkshopTest;

use AtomPie\Core\FrameworkConfig;
use AtomPie\Core\NamespaceHandler;
use AtomPie\System\Router;
use AtomPie\Web\Environment;
use AtomPiePhpUnitTest\ApplicationConfigDefinition;
use WorkshopTest\Resource\EndPoint\DefaultController;

class NamespaceHandlerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var FrameworkConfig
     */
    private $oConfig;

    public function setUp()
    {
        parent::setUp();
        $oEnvironment = Environment::getInstance();
        $this->oConfig = new FrameworkConfig(
            $oEnvironment,
            new Router(__DIR__.'/Routing/Routing.php'),
            new ApplicationConfigDefinition($oEnvironment->getEnv()),
            __DIR__ . '/../../../',
            __DIR__ . '/../../../WorkshopTest/Resource/Theme',
            [
                "\\WorkshopTest\\Resource\\EndPoint",
                "\\WorkshopTest\\Resource\\Component",
                'WorkshopTest\Resource\Operation',
            ],
            [
                "\\WorkshopTest\\Resource\\Component",
                "\\WorkshopTest\\Resource"
            ],
            [
                'Test1\\Class4',
                "\\WorkshopTest\\Resource\\EndPoint\\DefaultController"
            ]
        );
    }

    public function testShortener()
    {

        // Search via namespace
        $oShortener = new NamespaceHandler(
            [
                'Test1\\Test2\\Test3\\Test4',
                '\\Test1\\Test2\\Test3',
            ]
        );
        $this->assertTrue($oShortener->shorten('Test1\\Test2\\Test3\\Test4\\Class1') == 'Class1');
        $this->assertTrue($oShortener->shorten('Test1\\Test2\\Test3\\Test5\\Class1') == 'Test5\\Class1');

        $this->assertTrue($oShortener->shorten('\\Test1\\Test2\\Test3\\Test4\\Class1', false) == '\\Class1');
        $this->assertTrue($oShortener->shorten('Test1\\Test2\\Test3\\Test5\\Class1', false) == '\\Test5\\Class1');

        $this->assertTrue($oShortener->shorten('Test2\\Test3\\Test5\\Class1') == 'Test2\\Test3\\Test5\\Class1');
        $this->assertTrue($oShortener->shorten('\\Test2\\Test3\\Test5\\Class1',
                false) == '\\Test2\\Test3\\Test5\\Class1');
        $this->assertTrue($oShortener->shorten('\\Test2\\Test3\\Test5\\Class1') == 'Test2\\Test3\\Test5\\Class1');

        // Search via full class
        $oShortener = new NamespaceHandler(null, $this->oConfig->getEndPointClasses());
        $this->assertTrue($oShortener->shorten('Test1\\Class4') == 'Class4');
    }

    public function testNamespaceFetch()
    {
        $oHandler = new NamespaceHandler($this->oConfig->getEndPointNamespaces());
        $this->assertTrue("\\WorkshopTest\\Resource\\EndPoint" == $oHandler->getNamespaceForClass(DefaultController::Type()->getName()));

        $oHandler = new NamespaceHandler(null, $this->oConfig->getEndPointClasses());
        $this->assertTrue("\\WorkshopTest\\Resource\\EndPoint" == $oHandler->getNamespaceForClass(DefaultController::Type()->getName()));

    }

    public function testFullClassFetch()
    {
        // Search via namespace
        $oHandler = new NamespaceHandler($this->oConfig->getEndPointNamespaces());
        $this->assertTrue("WorkshopTest\\Resource\\EndPoint\\DefaultController" == $oHandler->getFullClassName(DefaultController::Type()->getName()));

        // Search via full class
        $oShortener = new NamespaceHandler(null, $this->oConfig->getEndPointClasses());
        $this->assertTrue("\\WorkshopTest\\Resource\\EndPoint\\DefaultController" == $oShortener->getFullClassName(DefaultController::Type()->getName()));

    }

    public function testMergeNamespace_OK()
    {
        $oHandler = new NamespaceHandler($this->oConfig->getEndPointNamespaces());
        list($sClass, $sNamespace) = $oHandler->mergeNamespaceToClass('Namespace1\\Test1\\Test2\\Test3',
            'Test2\\Test3\\Class1');
        $this->assertTrue($sNamespace == 'Namespace1\\Test1');
        $this->assertTrue($sClass == 'Namespace1\\Test1\\Test2\\Test3\\Class1');

        list($sClass, $sNamespace) = $oHandler->mergeNamespaceToClass('Namespace1\\Test1\\Test2\\Test3',
            '\\Test3\\Class1');
        $this->assertTrue($sNamespace == 'Namespace1\\Test1\\Test2');
        $this->assertTrue($sClass == 'Namespace1\\Test1\\Test2\\Test3\\Class1');

        list($sClass, $sNamespace) = $oHandler->mergeNamespaceToClass('Namespace1\\Test1\\Test2\\Test3',
            'Namespace1\\Test1\\Test2\\Test3\\Class1');
        $this->assertTrue($sNamespace == 'Namespace1\\Test1\\Test2\\Test3');
        $this->assertTrue($sClass == 'Namespace1\\Test1\\Test2\\Test3\\Class1');

        list($sClass, $sNamespace) = $oHandler->mergeNamespaceToClass('Namespace1\\Test1\\Test2\\Test3',
            '\\Namespace1\\Test1\\Test2\\Test3\\Class1');
        $this->assertTrue($sNamespace == 'Namespace1\\Test1\\Test2\\Test3');
        $this->assertTrue($sClass == 'Namespace1\\Test1\\Test2\\Test3\\Class1');
    }

    public function testMergeNamespace_NotOK()
    {
        $oHandler = new NamespaceHandler($this->oConfig->getEndPointNamespaces());
        list($sClass, $sNamespace) = $oHandler->mergeNamespaceToClass('Namespace1\\Test1\\Test2\\Test3',
            'Test4\\Test3\\Class1');
        $this->assertTrue($sNamespace == 'Namespace1\\Test1\\Test2\\Test3');
        $this->assertTrue($sClass == 'Test4\\Test3\\Class1');

        list($sClass, $sNamespace) = $oHandler->mergeNamespaceToClass('Namespace1\\Test1\\Test2\\Test3', 'st3\\Class1');
        $this->assertTrue($sNamespace == 'Namespace1\\Test1\\Test2\\Test3');
        $this->assertTrue($sClass == 'st3\\Class1');
    }

}
