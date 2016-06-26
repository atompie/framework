<?php
namespace AtomPiePhpUnitTest\System;

use AtomPie\Gui\ViewTree\ViewIterator;
use AtomPieTestAssets\Resource\Mock\Node1;
use AtomPieTestAssets\Resource\Mock\Node2;
use AtomPieTestAssets\Resource\Mock\Node4;
use AtomPie\System\IO\File;

class ViewIteratorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function shouldReturnFilledTemplateOfNode1()
    {
        $sNode1 = $this->render(new Node1());
        $oNode1 = json_decode($sNode1);
        $this->assertTrue(isset($oNode1->value1) && $oNode1->value1 == 'value1');
        $this->assertTrue(isset($oNode1->value2) && $oNode1->value2 == 'value2');
        $this->assertTrue(isset($oNode1->value3) && $oNode1->value3 == "abx");
    }

    /**
     * @test
     */
    public function shouldReturnFilledTemplateOfNode4()
    {
        $sNode4 = $this->render(new Node4());
        $oNode4 = json_decode($sNode4);
        $this->assertTrue(isset($oNode4->Node1->value1) && $oNode4->Node1->value1 == 'value1');
        $this->assertTrue(isset($oNode4->Node1->value2) && $oNode4->Node1->value2 == 'value2');
        $this->assertTrue(isset($oNode4->Node1->value3) && $oNode4->Node1->value3 == "abx");
        $this->assertTrue(isset($oNode4->Node1s[0]->value1) && $oNode4->Node1s[0]->value1 == 'value1');
        $this->assertTrue(isset($oNode4->Node1s[0]->value2) && $oNode4->Node1s[0]->value2 == 'value2');
        $this->assertTrue(isset($oNode4->Node1s[0]->value3) && $oNode4->Node1s[0]->value3 == "abx");
    }

    /**
     * @test
     */
    public function shouldReturnFilledTemplateOfNode2()
    {
        $sNode2 = $this->render(new Node2());
        var_dump($sNode2);
        $oNode2 = json_decode($sNode2);
        var_dump($oNode2);

    }

    private function render($oComponent)
    {
        $oMustache = new \Mustache_Engine();
        $oIterator = new ViewIterator(
            __DIR__ . DIRECTORY_SEPARATOR . '../../AtomPieTestAssets/Resource/Mock',
            function ($sTemplatePath, $aPlaceHolders) use ($oMustache) {
                $oFile = new File($sTemplatePath);
                return $oMustache->render($oFile->loadRaw(), $aPlaceHolders);
            }
        );
        return $oIterator->renderComponent($oComponent);
    }
}
