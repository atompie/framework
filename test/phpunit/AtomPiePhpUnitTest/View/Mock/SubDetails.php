<?php
namespace AtomPiePhpUnitTest\View\Mock {

    use AtomPie\View\Boundary\ICanBeRendered;

    class SubDetails implements ICanBeRendered
    {

        private $sAddress;

        public function __construct($sAddress)
        {
            $this->sAddress = $sAddress;
        }

        public function getViewPlaceHolders()
        {
            return array(
                'address' => $this->sAddress,
                'array' => array('a', 'b', 'c'),
                'item' => 'xxx'
            );
        }

        public function getTemplateFile($sFolder)
        {
            return __DIR__ . DIRECTORY_SEPARATOR . 'SubDetails.twig';
        }

    }

}
