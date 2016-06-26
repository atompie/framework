<?php
namespace AtomPiePhpUnitTest\Gui;

use AtomPie\Gui\Component;
use AtomPie\Gui\ViewTree\ViewIterator;
use AtomPie\System\IO\File;
use AtomPieTestAssets\Resource\Mock\MockComponent10;
use AtomPieTestAssets\Resource\Mock\MockComponent9;
use WorkshopTest\Boot;

class TemplatePlaceHolderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldRenderMustacheTemplate()
    {

        MockComponent9::injectDependency(Boot::getComponentDi());
        $oComponent = new MockComponent9();
        $oMustache = new \Mustache_Engine();
        $oIterator = new ViewIterator(
            realpath(__DIR__ . '/../../AtomPieTestAssets'),
            function (File $oTemplateFile, $aPlaceHolders) use ($oMustache) {
                return $oMustache->render($oTemplateFile->loadRaw(), $aPlaceHolders);
            }
        );
        $sContentByMustache = $oIterator->renderComponent($oComponent);
//        echo $sContentByMustache;

        $this->assertTrue($oComponent->LoginForm instanceof MockComponent10);
        $this->assertTrue(is_array($oComponent->List));
    }
}
