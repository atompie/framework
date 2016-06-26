<?php
namespace AtomPie\Html\Form\Field\Element {

    use AtomPie\Html\Form\Field\Element;

    class SubmitButton extends Input
    {

        public function __construct($sName, $sLabel = 'Submit')
        {
            parent::__construct($sName, $sLabel, Input::SUBMIT);
            $this->closeTag(false);
        }

    }

}