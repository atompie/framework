<?php
namespace AtomPieTestAssets\Resource\Mock {

    use AtomPie\View\Boundary\ICanBeRendered;
    use AtomPie\System\IO\File;

    class Node1 implements ICanBeRendered
    {

        /**
         * @return array
         */
        public function getViewPlaceHolders()
        {

            $oClass = new \stdClass;
            $oClass->x = 'x';
            $oClass->y = 'y';
            return [
                'value1' => 'value1',
                'value2' => 'value2',
                'value3' => [
                    'a' => 'a',
                    'b' => 'b',
                    'c' => $oClass
                ]
            ];
        }

        /**
         * @param $sFolder
         * @return File
         */
        public function getTemplateFile($sFolder)
        {
            return $sFolder . DIRECTORY_SEPARATOR . 'Node1.mustache';
        }
    }

}
