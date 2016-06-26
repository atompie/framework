<?php
namespace AtomPie\Gui\ViewTree {

    use AtomPie\View\Boundary\ICanBeRendered;
    use Generi\Boundary\IStringable;

    class ObjectNode
    {

        /**
         * @var array
         */
        private $aData;

        public function __construct(array $aData)
        {
            $this->aData = $aData;
        }

        /**
         * @return array
         */
        public function getObjectProperties()
        {
            return $this->aData;
        }
    }

    class ViewIterator
    {

        private $pRenderClosure;
        private $sViewFolder;

        public function __construct($sViewFolder, $pRenderClosure)
        {
            $this->pRenderClosure = $pRenderClosure;
            $this->sViewFolder = $sViewFolder;
        }

        public function iterate(array $aPlaceHolders, $aViewTree)
        {

            foreach ($aPlaceHolders as $sPlaceHolder => $mNode) {

                if ($mNode instanceof ICanBeRendered) {

                    $sNodeString = $this->renderComponent($mNode);

                } else {
                    if (is_array($mNode)) {

                        $aViewTree[$sPlaceHolder] = $this->iterate($mNode, []);
                        continue;

                    } else {
                        if ($mNode instanceof IStringable) {

                            $sNodeString = $mNode->__toString();

                        } else {

                            $sNodeString = $mNode;

                        }
                    }
                }

                $aViewTree[$sPlaceHolder] = $sNodeString;
            }

            return $aViewTree;

        }

        /**
         * @param $oComponent
         * @return mixed
         */
        public function renderComponent(ICanBeRendered $oComponent)
        {
            $aPlaceHolders = $this->iterate($oComponent->getViewPlaceHolders(), []);
            $sTemplatePath = $oComponent->getTemplateFile($this->sViewFolder);
            $pClosure = $this->pRenderClosure;
            return $pClosure($sTemplatePath, $aPlaceHolders);
        }

    }

}
