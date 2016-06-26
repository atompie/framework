<?php
namespace AtomPieTestAssets\Resource\Mock {

    use AtomPie\View\Boundary\ICanBeRendered;
    use AtomPie\System\IO\File;

    class Node3 implements ICanBeRendered
    {

        /**
         * @return array
         */
        public function getViewPlaceHolders()
        {
            return [
                'node8' => 'value8'
            ];
        }

        /**
         * @param $sFolder
         * @return File
         */
        public function getTemplateFile($sFolder)
        {
            return $sFolder . DIRECTORY_SEPARATOR . 'Node3.mustache';
        }
    }

}
