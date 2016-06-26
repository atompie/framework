<?php
namespace AtomPie\Html\Tag {

    use AtomPie\Html\Attribute;
    use AtomPie\Html\ElementNode;
    use AtomPie\Html\TextNode;

    class TitleSpan extends ElementNode
    {
        public function __construct($sTitleLabel, $sText, $sCssClass = null)
        {
            parent::__construct('span');
            $this->addAttribute(new Attribute('title', $sTitleLabel));
            $this->addChild(new TextNode($sText));
            if (!is_null($sCssClass)) {
                $this->addAttribute(new Attribute('class', $sCssClass));
            }
        }
    }
}