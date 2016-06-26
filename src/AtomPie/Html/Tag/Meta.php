<?php
namespace AtomPie\Html\Tag {

    use AtomPie\Html;
    use Generi\Boundary;

    class Meta extends Html\ElementNode
    {
        public function __construct($sName, $sContent)
        {
            parent::__construct('meta');
            $this->addAttribute(new Html\Attribute('name', $sName));
            $this->addAttribute(new Html\Attribute('content', $sContent));
        }
    }
}