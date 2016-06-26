<?php
namespace AtomPie\Html {

    class ReferenceElementNode
    {
        /**
         * @var \AtomPie\Html\TagNode
         */
        private $oReferenceElementNode;
        /**
         * @var array
         */
        private $aChildNodes = array();

        /**
         * @param \AtomPie\Html\TagNode $oReferenceElementNode
         */
        public function __construct(TagNode $oReferenceElementNode)
        {
            $this->oReferenceElementNode = $oReferenceElementNode;
        }

        /**
         * @return \AtomPie\Html\ElementNode
         */
        public function getReferencedNode()
        {
            return $this->oReferenceElementNode;
        }

        final public function addChild(ReferenceElementNode $oNode)
        {
            // Add namespace if it has namespace
            $this->aChildNodes[] = $oNode;
        }

        final public function hasChildren()
        {
            return !empty($this->aChildNodes);
        }

        final public function getChildren()
        {
            return $this->aChildNodes;
        }

        /////////////////////////////

        final public function __toString()
        {
            if ($this->hasChildren()) {
                return self::renderStart($this->oReferenceElementNode) . implode('',
                    $this->getChildren()) . self::renderEnd($this->oReferenceElementNode);
            } else {
                return self::renderClosedStart($this->oReferenceElementNode);
            }
        }

        /**
         * Returns opening tag string.
         *
         * @param TagNode $oElement
         * @return string
         */
        private static function renderStart(TagNode $oElement)
        {
            if ($oElement instanceof ElementNode) {
                $sTag = ($oElement->hasAttributes())
                    ? $oElement->getTagName() . ' ' . $oElement->getAttributes()->__toString()
                    : $oElement->getTagName();

                return '<' . $sTag . '>';
            }

            return '<' . $oElement->getTagName() . '>';
        }

        /**
         * Returns closed opening tag string.
         *
         * @param TagNode $oElement
         * @return string
         */
        private static function renderClosedStart(TagNode $oElement)
        {
            if ($oElement instanceof ElementNode) {
                $sTag = ($oElement->hasAttributes())
                    ? $oElement->getTagName() . ' ' . $oElement->getAttributes()->__toString()
                    : $oElement->getTagName();
                return '<' . $sTag . ' />';
            }

            return '<' . $oElement->getTagName() . '>';
        }

        /**
         * Returns close tag.
         *
         * @param TagNode $oElement
         * @return string
         */
        private static function renderEnd(TagNode $oElement)
        {
            return '</' . $oElement->getTagName() . '>';
        }
    }
}