<?php
namespace AtomPie\Html\Tag {

    use AtomPie\Html\Attribute;
    use AtomPie\Html\ElementNode;

    class Image extends ElementNode
    {
        public function __construct($sImagePath)
        {
            parent::__construct('img');
            $this->addAttribute(new Attribute('src', $sImagePath));
        }
    }
}