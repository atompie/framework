<?php
namespace AtomPiePhpUnitTest\View;

use AtomPie\View\TwigLikeView;
use AtomPie\View\TwigTemplateFile;
use AtomPiePhpUnitTest\View\Mock\Details;
use AtomPie\System\IO\File;

class TwigLikeViewTest extends \PHPUnit_Framework_TestCase
{

    public function testRenderTemplate()
    {
        $sFolder = __DIR__ . '/Resource';

        $oFile = new File($sFolder . '/View1.twig');

        $oView = new TwigLikeView($sFolder);
        $sView = $oView->fillTemplate(array('test' => 'testValue'), $oFile->loadRaw());
        $this->assertTrue($sView == 'testValue');
    }

    public function testRenderTemplateWithArray1()
    {
        $sFolder = __DIR__ . '/Resource';

        $oView = new TwigLikeView($sFolder);
        $sView = $oView->renderTemplate(
            [
                'test1' => ['item1' => 1, 'item2' => 2],
                'test2' => 'test-string'
            ],
            new TwigTemplateFile($sFolder . '/View2.twig')
        );

        print($sView);
    }

    public function testRenderTemplateWithArray2()
    {
        $sFolder = __DIR__ . '/Resource';

        $oView = new TwigLikeView($sFolder);
        $sView = $oView->renderTemplate(
            [
                'test2' => 'test-string'
            ],
            new TwigTemplateFile($sFolder . '/View5.twig')
        );

        print($sView);
    }

    public function testRenderTemplateWithObject1()
    {
        $sFolder = __DIR__ . '/Resource';

        $oView = new TwigLikeView($sFolder);
        $sView = $oView->renderTemplate(
            array('test' => new Details('Risto', 'Kowaczewski')),
            new TwigTemplateFile($sFolder . '/View4.twig')
        );

        print($sView);
//		$this->assertTrue($sView == 'Details: Risto Kowaczewski');
    }


    public function testRenderTemplateWithObject2()
    {
        $sFolder = __DIR__ . '/Resource';
        $oObject = new Mock\SubDetails('Warszawska');

        $oFile = new File($oObject->getTemplateFile($sFolder));

        $oView = new TwigLikeView($sFolder);
        $sView = $oView->fillTemplate($oObject->getViewPlaceHolders(), $oFile->loadRaw());
//		$this->assertTrue($sView == 'ul. Warszawska');

        echo $sView;
    }


    public function testRenderTemplateWithObject3()
    {
        $sFolder = __DIR__ . '/Resource';
        $oObject = new Details('Jan', 'Kowalski');

        $oFile = new File($oObject->getTemplateFile($sFolder));

        $oView = new TwigLikeView($sFolder);
        $sView = $oView->fillTemplate($oObject->getViewPlaceHolders(), $oFile->loadRaw());
//		$this->assertTrue($sView == 'ul. Warszawska');

        echo $sView;
    }

    public function testRenderTemplate_Block()
    {
        $sFolder = __DIR__ . '/Resource';
        $oFile = new TwigTemplateFile($sFolder . '/View3.twig');

        $oView = new TwigLikeView($sFolder);
        echo $oView->renderTemplate(
            array(
                'columns' => array(
                    new Details('Risto', 'Kowaczewski'),
                    new Details('Jan', 'Kowalski'),
                ),
                'list' => array(
                    array('item' => 1),
                    array('item' => 2),
                    array('item' => 3)
                ),
                'Header1Name' => 'column1',
                'Header2Name' => 'column2',
            ),
            $oFile
        );
    }
}
