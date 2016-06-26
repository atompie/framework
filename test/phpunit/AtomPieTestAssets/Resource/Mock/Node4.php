<?php
namespace AtomPieTestAssets\Resource\Mock {

    use AtomPie\View\Boundary\ICanBeRendered;
    use AtomPie\System\IO\File;

    class Node4 implements ICanBeRendered
    {

        /**
         * @return array
         */
        public function getViewPlaceHolders()
        {
            return [
                'node1' => new Node1(),
                'node1s' => [
                    new Node1(),
                ]
            ];
        }

        /**
         * @param $sFolder
         * @return File
         */
        public function getTemplateFile($sFolder)
        {
            return $sFolder . DIRECTORY_SEPARATOR . 'Node4.mustache';
        }
    }

}
