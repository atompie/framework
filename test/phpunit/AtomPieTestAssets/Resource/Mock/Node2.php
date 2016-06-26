<?php
namespace AtomPieTestAssets\Resource\Mock {

    use AtomPie\View\Boundary\ICanBeRendered;
    use AtomPie\System\IO\File;

    class Node2 implements ICanBeRendered
    {

        /**
         * @return array
         */
        public function getViewPlaceHolders()
        {
            return [
                'list1' => [
                    ['item' => new Node1(), 'comma' => ','],
                    ['item' => new Node4()]
                ],
                'list2' => [
                    ["item" => 'a', 'comma' => ','],
                    ["item" => 'b', 'comma' => ','],
                    ["item" => 'c']
                ]

            ];
        }

        /**
         * @param $sFolder
         * @return File
         */
        public function getTemplateFile($sFolder)
        {
            return $sFolder . DIRECTORY_SEPARATOR . 'Node2.mustache';
        }
    }

}
