<?php
namespace AtomPie\Html\Form\Field\Element {

    use AtomPie\Html\Attribute;
    use AtomPie\Html\Form\Field\Element;

    class ResetButton extends Element
    {

        public function __construct($sName, $sLabel = 'Reset')
        {
            parent::__construct('input', $sName);
            $this->closeTag();
            $this->setValue($sLabel);
        }

        public function setDisabled($bDisabled)
        {
            if ($bDisabled) {
                $this->addAttribute(new Attribute('disabled', 'disabled'));
            } else {
                $this->removeAttribute('disabled');
            }
        }

        protected function beforeToString()
        {
            $this->addAttribute(new Attribute('type', 'reset'));
            $this->addAttribute(new Attribute('name', $this->getName()));
            $this->addAttribute(new Attribute('value', $this->getValue()));
        }

    }

}