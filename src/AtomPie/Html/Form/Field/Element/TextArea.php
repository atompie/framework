<?php
namespace AtomPie\Html\Form\Field\Element {

    use AtomPie\Html\Attribute;
    use AtomPie\Html\Form\Field\Element;
    use AtomPie\Html\TextNode;

    class TextArea extends Element
    {

        public $sId;
        public $sLabel;
        public $aData = array();

        public function __construct($sName, $sValue = '')
        {
            parent::__construct('textarea', $sName);
            $this->setValue($sValue);
        }

        protected function beforeToString()
        {
            $this->addAttribute(new Attribute('name', $this->getName()));
            $this->addAttribute(new Attribute('id', $this->sId));
            $this->removeChildren();
            $this->addChild(new TextNode($this->sValue));
        }

    }
}