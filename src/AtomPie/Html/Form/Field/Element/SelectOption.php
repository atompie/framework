<?php
namespace AtomPie\Html\Form\Field\Element {

    use AtomPie\Html\Attribute;
    use AtomPie\Html\ElementNode;
    use AtomPie\Html\TextNode;
    use Generi\Boundary\IValuable;

    class SelectOption extends ElementNode implements IValuable
    {

        public $aData = array();
        private $bSelected = false;
        private $sKey;
        private $sValue;

        public function __construct($sKeyValue, $sPrintValue = '', $bSelected = false)
        {
            parent::__construct('option');
            $this->sKey = $sKeyValue;
            $this->sValue = $sPrintValue;

            if ($bSelected) {
                $this->setSelected();
            }
        }

        public function setSelected()
        {
            $this->bSelected = true;
            $this->addAttribute(new Attribute('selected', 'selected'));
        }

        public function removeSelected()
        {
            $this->bSelected = false;
            $this->removeAttribute('selected');
        }

        public function isSelected()
        {
            return $this->bSelected;
        }

        ///////////////////////
        // IValuable

        /**
         * @param mixed $sValue
         */
        public function setValue($sValue)
        {
            $this->sValue = $sValue;
        }

        /**
         * Returns value from IValuable object.
         *
         * @return mixed
         */
        public function getValue()
        {
            return $this->sValue;
        }

        /**
         * @return bool
         */
        public function isEmpty()
        {
            return empty($this->sValue);
        }

        /////////////////////////

        public function setKey($sKey)
        {
            $this->sKey = $sKey;
        }

        public function getKey()
        {
            return $this->sKey;
        }

        protected function beforeToString()
        {
            $this->removeAttribute('value');
            $this->removeChildren();
            $this->addAttribute(new Attribute('value', (string)$this->sKey));
            $this->addChild(new TextNode($this->sValue));
        }
    }

}

