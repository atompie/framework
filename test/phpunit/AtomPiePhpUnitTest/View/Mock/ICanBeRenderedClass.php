<?php
namespace AtomPiePhpUnitTest\View\Mock {

    use AtomPie\View\Boundary\ICanBeRendered;

    class ICanBeRenderedClass implements ICanBeRendered
    {

        public function getViewPlaceHolders()
        {
            return array('PlaceHolder1' => 'value1', 'PlaceHolder2' => 'value2');
        }

        public function getTemplateFile($sFolder)
        {
            return __DIR__ . '/ICanBeRenderedClass.twig';
        }
    }

}
