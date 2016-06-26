<?php
namespace AtomPie\Html\Tag {

    use AtomPie\Html\Attribute;
    use AtomPie\Html\ElementNode;
    use AtomPie\Html\TextNode;

    class TableHeader extends ElementNode
    {

        public $HeaderName;

        public function __construct($sHeaderName, $sClass = null)
        {
            parent::__construct('th');
            $this->addChild(new TextNode($sHeaderName));
            if (!is_null($sClass)) {
                $this->addAttribute(new Attribute('class', $sClass));
            }
        }
    }

}