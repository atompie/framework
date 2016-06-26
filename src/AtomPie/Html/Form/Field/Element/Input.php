<?php
namespace AtomPie\Html\Form\Field\Element {

    use AtomPie\Html\Attribute;
    use AtomPie\Html\Form\Field\Element;
    use AtomPie\Html\Boundary\ITypable;

    /**
     * Element of the html form.
     *
     * @author risto
     */
    class Input extends Element implements ITypable
    {

        const HIDDEN = 'hidden';
        const PASSWORD = 'password';
        const TEXT = 'text';
        const SUBMIT = 'submit';
        const RESET = 'reset';
        const DATE = 'date';
        const TIME = 'time';
        const DATETIME = 'datetime';
        const RANGE = 'range';
        const DATETIME_LOCAL = 'datetime-local';
        const COLOR = 'color';
        const MONTH = 'month';
        const NUMBER = 'number';
        const SEARCH = 'search';
        const TEL = 'tel';
        const URL = 'url';
        const WEEK = 'week';
        const EMAIL = 'email';

        private $sType;

        public function __construct($sName, $sValue = '', $sType = null)
        {
            parent::__construct('input', $sName);

            $this->setValue($sValue);
            $this->sType = (!is_null($sType)) ? $sType : self::TEXT;
        }

        ///////////////////////////
        // ITypable

        public function setType($sType)
        {
            $this->sType = $sType;
        }

        public function getType()
        {
            return $this->sType;
        }

        /////////////////////////////

        public function setDisabled($bDisabled)
        {
            if ($bDisabled) {
                $this->addAttribute(new Attribute('disabled', 'disabled'));
            } else {
                $this->removeAttribute('disabled');
            }
        }

        public function isEqual($sValue)
        {
            if (is_null($sValue)) {
                return false;
            }
            return $sValue == $this->getValue();
        }

        public function setPlaceholder($sValue)
        {
            $this->addAttribute(new Attribute('placeholder', $sValue));
        }

        protected function beforeToString()
        {
            $this->addAttribute(new Attribute('type', $this->sType));
            $this->addAttribute(new Attribute('name', $this->getName()));
            if ($this->hasId()) {
                $this->addAttribute(new Attribute('id', $this->getId()));
            }
            $this->addAttribute(new Attribute('value', $this->getValue()));
        }

    }

}