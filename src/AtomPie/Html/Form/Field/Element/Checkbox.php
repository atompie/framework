<?php
namespace AtomPie\Html\Form\Field\Element {

    use AtomPie\Html\Form\Field;

    class Checkbox extends CheckableInput
    {
        public function __construct($sName, $sValue = '')
        {
            parent::__construct($sName, $sValue, 'checkbox');
            $this->setId(null);
            $this->closeTag(false);
        }
    }

}
