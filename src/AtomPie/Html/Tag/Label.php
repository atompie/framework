<?php
namespace AtomPie\Html\Tag {

    use AtomPie\Html\ElementNode;
    use AtomPie\Html\TextNode;

    class Label extends ElementNode
    {
        public function __construct($sLabel)
        {
            parent::__construct('label');
            $this->addChild(new TextNode($sLabel));
        }
    }
}
