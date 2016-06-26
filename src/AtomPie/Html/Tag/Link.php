<?php
namespace AtomPie\Html\Tag {

    use AtomPie\Html\Attribute;
    use AtomPie\Html\CharacterDataNode;
    use AtomPie\Html\ElementNode;
    use AtomPie\Html\TextNode;
    use AtomPie\Html\Exception;

    class Link extends ElementNode
    {

        private $oChild;

        public function __construct($sUrl = null, $oChild = null)
        {
            parent::__construct('a');

            if (!is_null($sUrl)) {
                $this->addAttribute(new Attribute('href', $sUrl));
            }
            if (!is_null($oChild)) {
                if ($oChild instanceof ElementNode) {
                    $this->oChild = $oChild;
                } else {
                    $this->oChild = new TextNode((string)$oChild);
                }
                $this->addChild($this->oChild);
            }
        }

        /**
         * @param string $sText
         * @throws Exception
         */
        public function setText($sText)
        {
            if (is_null($this->oChild)) {
                $this->oChild = new TextNode($sText);
                $this->addChild($this->oChild);
            } else {
                if ($this->oChild instanceof CharacterDataNode) {
                    $this->oChild->sTextContent = $sText;
                } else {
                    throw new Exception('Inner child is not \Html\CharacterDataNode but is [' . get_class($this->oChild) . ']!');
                }
            }
        }
    }
}