<?php
namespace AtomPiePhpUnitTest\View\Mock {

    use AtomPie\View\Boundary\ICanBeRendered;

    class Details implements ICanBeRendered
    {

        private $Name;
        private $Surname;

        public function __construct($sName, $sSurname)
        {
            $this->Name = $sName;
            $this->Surname = $sSurname;
        }

        public function getViewPlaceHolders()
        {

            $oStd = new \stdClass();
            $oStd->Zip = '55-555';
            return array(
                'Name' => $this->Name,
                'Surname' => $this->Surname,
                'addresses' => array(
                    new SubDetails('Warszawska'),
                    new SubDetails('Lubanska'),
                    new ICanBeRenderedClass()
                ),
                'code' => $oStd
            );
        }

        public function getTemplateFile($sFolder)
        {
            return __DIR__ . DIRECTORY_SEPARATOR . 'Details.twig';
        }

    }

}
