<?php
namespace AtomPie\Html {

    use Generi\Boundary\IStringable;
    use AtomPie\Html;

    /**
     * Holds responsibility of:
     *
     * 1. Namespace
     * 2. Tag name
     * 3. Tree of tags
     */
    abstract class TagNode extends Node implements Boundary\IHaveTagNamespace, Boundary\ITagTree
    {
        /**
         * Holds node tag name.
         *
         * @var string
         */
        private $sTagName;
        /**
         * Holds namespace of node.
         *
         * @var string
         */
        private $sNamespace;
        /**
         * Holds child nodes.
         *
         * @var array
         */
        private $aChildNodes;
        /**
         * Holds child nodes divided by namespace.
         *
         * @var array
         */
        private $aChildNodesByNamespace = array();
        /**
         * Holds child nodes indexed by tag names
         *
         * @var array
         */
        private $aChildNodesByTagName = array();
        private $aInternalElementsByTagName = array();
        /**
         * Hold information on node content.
         *
         * @var bool
         */
        protected $bHasContent;
        /**
         * Show tag close even if empty content, eg. <tag></tag>
         * instead of <tag />.
         *
         * @var bool
         */
        protected $bShowCloseTagOnEmptyContent = false;

        public function __construct($sTagName, $sNamespace = null)
        {
            $this->sTagName = $sTagName;
            $this->sNamespace = $sNamespace;
            $this->iNodeType = self::XML_ELEMENT_NODE;
            $this->sTagOpen = '<';
            $this->sTagClose = '>';
        }

        ///////////////////////////
        // \Html\ITagNamespace

        final public function getNamespace()
        {
            return $this->sNamespace;
        }

        final public function setNamespace($sNamespace)
        {
            $this->sNamespace = $sNamespace;
        }

        final public function hasNamespace()
        {
            return !empty($this->sNamespace);
        }

        /////////////////////////////
        // \Html\ITagTree

        final public function addChild(Node $oNode, $sKey = null)
        {
            // Add namespace if it has namespace
            if ($oNode instanceof Boundary\IHaveTagNamespace && $oNode->hasNamespace()) {
                /** @var Node | Boundary\IHaveTagNamespace $oNode */
                $this->addChildWithNamespace($oNode, $oNode->getNamespace(), $sKey);
            }

            if (is_null($sKey)) {
                $this->aChildNodes[] = $oNode;
            } else {
                $this->aChildNodes[(string)$sKey] = $oNode;
            }

            if ($oNode instanceof TagNode) {
                /** @var Node | Boundary\IHaveTagNamespace | TagNode $oNode */
                $this->aChildNodesByTagName[$oNode->getTagName()][] = $oNode;
            }
        }

        final public function addInnerHtmlChild($sString, $sKey = null)
        {

            if (is_object($sString)) {
                if (!$sString instanceof IStringable) {
                    throw new Exception('Only string or \IStringable object can be added as InnerHtml.');
                }
                $sString = $sString->__toString();
            }

            if (is_null($sKey)) {
                $this->aChildNodes[] = $sString;
            } else {
                $this->aChildNodes[(string)$sKey] = $sString;
            }
        }

        private function addChildWithNamespace(Node $oNode, $sNamespace, $sKey = null)
        {
            if (is_null($sKey)) {
                $this->aChildNodesByNamespace[$sNamespace][] = $oNode;
            } else {
                $this->aChildNodesByNamespace[$sNamespace][(string)$sKey] = $oNode;
            }
        }

        final public function hasChild($sKey, $sNamespace = null)
        {
            if (is_null($sNamespace)) {
                return isset($this->aChildNodes[$sKey]);
            }
            return isset($this->aChildNodesByNamespace[$sNamespace][(string)$sKey]);
        }

        final public function removeChild($iNodeNumber, $sNamespace = null)
        {
            if (is_null($sNamespace)) {
                unset($this->aChildNodes[$iNodeNumber]);
            }
            unset($this->aChildNodesByNamespace[$sNamespace][$iNodeNumber]);
            unset($this->aChildNodes[$iNodeNumber]);
        }

        /**
         * @param string
         * @param string
         * @return \AtomPie\Html\TagNode
         */
        final public function getChild($iNodeNumber, $sNamespace = null)
        {
            if (!is_null($sNamespace)) {
                return $this->aChildNodesByNamespace[$sNamespace][$iNodeNumber];
            }
            return $this->aChildNodes[$iNodeNumber];
        }

        final public function removeChildren($sNamespace = null)
        {
            if (!is_null($sNamespace)) {
                if (isset($this->aChildNodesByNamespace[$sNamespace])) {
                    foreach ($this->aChildNodesByNamespace[$sNamespace] as $iNodeNumber => $oTag) {
                        $this->removeChild($iNodeNumber);
                    }
                }
                unset($this->aChildNodesByNamespace[$sNamespace]);
            } else {
                $this->aChildNodes = array();
            }
        }

        final public function hasChildren($sNamespace = null)
        {
            if (is_null($sNamespace)) {
                return !empty($this->aChildNodes);
            }
            return isset($this->aChildNodesByNamespace[$sNamespace]) && !empty($this->aChildNodesByNamespace[$sNamespace]);
        }

        /**
         * Returns only first-generation children and includes non-tagged text elements.
         *
         * @param null $sNamespace
         * @return array
         */
        final public function getChildren($sNamespace = null)
        {
            if (is_null($sNamespace)) {
                return $this->aChildNodes;
            }

            return isset($this->aChildNodesByNamespace[$sNamespace])
                ? $this->aChildNodesByNamespace[$sNamespace]
                : null;
        }

        /**
         * Returns first occurrence of child by id only from the first level of nesting.
         *
         * @param string $sId
         * @param string $sNamespace
         *
         * @return \AtomPie\Html\ElementNode[]
         */
        final public function getChildById($sId, $sNamespace = null)
        {
            if ($this->hasChildren($sNamespace)) {
                foreach ($this->getChildren($sNamespace) as $oChild) {
                    if ($oChild instanceof ElementNode && $oChild->hasAttribute('id', $sNamespace)) {
                        if ($oChild->getAttribute('id') == $sId) {
                            return $oChild;
                        }
                    }
                }
            }
            return null;
        }

        /**
         * Returns only first-generation (first level) children with given tag name.
         *
         * @param $sTagName
         * @return array
         */
        final public function getChildNodesByTagName($sTagName)
        {
            return isset($this->aChildNodesByTagName[$sTagName])
                ? $this->aChildNodesByTagName[$sTagName]
                : null;
        }

        /**
         * Returns all tagged elements regardless of their nest level and no white-space.
         * This should locate all elements with given tag name.
         *
         * @param string $sTagName
         * @return array
         */
        final public function getElementsByTagName($sTagName)
        {
            $this->aInternalElementsByTagName = array();
            $this->childNodesByTagName($this->getChildren(), $sTagName);
            return $this->aInternalElementsByTagName;
        }

        private function childNodesByTagName($aTags, $sTagName)
        {
            if (!empty($aTags)) {
                foreach ($aTags as $oElementNode) {
                    if ($oElementNode instanceof TagNode) {
                        if ($oElementNode->getTagName() == $sTagName) {
                            $this->aInternalElementsByTagName[] = $oElementNode;
                        }
                        if ($oElementNode->hasChildren()) {
                            $this->childNodesByTagName($oElementNode->getChildren(), $sTagName);
                        }
                    }
                }
            }
        }

        private $iInternalLevelCounting = 0;

        final public function getElementsTreeByNamespace($sNamespace, $iMaximumLevel = 1)
        {
            $this->iInternalLevelCounting = 0;
            $oTreeNodesByName = new ReferenceElementNode($this);
            $this->childTreeNodesByNamespace($this, $oTreeNodesByName, $sNamespace, $iMaximumLevel);
            return $oTreeNodesByName->getChildren();
        }

        private function childTreeNodesByNamespace(
            TagNode $oSourceNode,
            ReferenceElementNode $oDestinationNode,
            $sNamespace,
            $iMaximumLevel
        ) {
            $aTags = $oSourceNode->getChildren();

            if (!empty($aTags)) {
                foreach ($aTags as $oElementNode) {
                    if ($oElementNode instanceof TagNode) {
                        if ($oElementNode->getNamespace() == $sNamespace) {
                            // Prepare
                            $oTreeNode = new ReferenceElementNode($oElementNode);
                            // Add child to destination
                            $oDestinationNode->addChild($oTreeNode);
                            // Add level
                            $this->iInternalLevelCounting++;
                            // Traverse
                            if ($this->iInternalLevelCounting < $iMaximumLevel) {
                                $this->childTreeNodesByNamespace($oElementNode, $oTreeNode, $sNamespace,
                                    $iMaximumLevel);
                            }

                        } elseif ($oElementNode->hasChildren()) {
                            $this->childTreeNodesByNamespace($oElementNode, $oDestinationNode, $sNamespace,
                                $iMaximumLevel);
                        }
                    }
                }
            }
        }

        final public function getElementsTreeByTagName($sTagName, $iMaximumLevel = 1)
        {
            $this->iInternalLevelCounting = 0;
            $oTreeNodesByName = new ReferenceElementNode($this);
            $this->childTreeNodesByTagName($this, $oTreeNodesByName, $sTagName, $iMaximumLevel);
            return $oTreeNodesByName->getChildren();
        }

        private function childTreeNodesByTagName(
            TagNode $oSourceNode,
            ReferenceElementNode $oDestinationNode,
            $sTagName,
            $iMaximumLevel
        ) {
            $aTags = $oSourceNode->getChildren();

            if (!empty($aTags)) {
                foreach ($aTags as $oElementNode) {
                    if ($oElementNode instanceof TagNode) {
                        if ($oElementNode->getTagName() == $sTagName) {
                            // Prepare
                            $oTreeNode = new ReferenceElementNode($oElementNode);
                            // Add child to destination
                            $oDestinationNode->addChild($oTreeNode);
                            // Add level
                            $this->iInternalLevelCounting++;
                            // Traverse
                            if ($this->iInternalLevelCounting < $iMaximumLevel) {
                                $this->childTreeNodesByTagName($oElementNode, $oTreeNode, $sTagName, $iMaximumLevel);
                            }

                        } elseif ($oElementNode->hasChildren()) {
                            $this->childTreeNodesByTagName($oElementNode, $oDestinationNode, $sTagName, $iMaximumLevel);
                        }
                    }
                }
            }
        }

        ////////////////////////////

        final public function getTagName($bWithNamespace = true)
        {
            if ($this->hasNamespace() && $bWithNamespace) {
                return $this->sNamespace . ':' . $this->sTagName;
            }

            return $this->sTagName;
        }

        final public function setTagName($sTagName, $sNamespace = null)
        {
            $this->sNamespace = $sNamespace;
            $this->sTagName = $sTagName;
        }

        public function closeTag($bShowCloseTagOnEmptyContent = true)
        {
            $this->bShowCloseTagOnEmptyContent = $bShowCloseTagOnEmptyContent;
        }

    }

}