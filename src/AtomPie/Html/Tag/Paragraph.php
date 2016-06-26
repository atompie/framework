<?php
namespace AtomPie\Html\Tag {

    use AtomPie\Html\ElementNode;
    use AtomPie\Html\TextNode;

    class Paragraph extends ElementNode
    {
        public function __construct($sTextContent)
        {
            parent::__construct('p');
            $this->addChild(new TextNode((string)$sTextContent));
        }
    }
}
