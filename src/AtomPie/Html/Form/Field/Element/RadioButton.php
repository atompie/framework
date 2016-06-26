<?php
namespace AtomPie\Html\Form\Field\Element {

    use AtomPie\Html\Form\Field;

    class RadioButton extends CheckableInput
    {
        public function __construct($sName, $sValue = '')
        {
            parent::__construct($sName, $sValue, 'radio');
            $this->setId(null);
            $this->closeTag(false);
        }
    }
}