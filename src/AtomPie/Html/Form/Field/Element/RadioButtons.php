<?php
namespace AtomPie\Html\Form\Field\Element {

    use AtomPie\Html\Attribute;
    use AtomPie\Html\ElementNode;
    use AtomPie\Html\Form\Field\AbstractElement;
    use AtomPie\Html\Form\Field;
    use AtomPie\Html\Node;
    use AtomPie\Html\Tag\Label;
    use AtomPie\Html\TextNode;
    use Generi\Boundary\IValuable;

    class RadioButtons extends AbstractElement implements IValuable
    {
        /**
         * @var array
         */
        private $aRadioButtons;

        public function __construct($sName, array $aButtonValues)
        {
            parent::__construct('div', $sName);
            $this->addAttribute(new Attribute('class', 'CheckBoxesList'));

            $this->aRadioButtons = array();
            $iItemNo = 0;
            foreach ($aButtonValues as $sKey => $sValue) {
                $iItemNo++;
                $sId = $sName . '-' . $iItemNo;
                $oWrapper = new ElementNode('div');
                $oWrapper->addAttribute(new Attribute('class', 'CheckboxItem'));
                $oRadioButton = new Field\Element\RadioButton($sName, $sKey);
                $oRadioButton->addAttribute(new Attribute('id', $sId));
                $this->aRadioButtons[] = $oRadioButton;
                $oWrapper->addChild($oRadioButton);

                if (!is_null($sValue)) {
                    if (is_string($sValue)) {
                        $oLabel = new Label(new TextNode($sValue));
                        $oLabel->addAttribute(new Attribute('for', $sId));
                        $oWrapper->addChild($oLabel);
                    } else {
                        if ($sValue instanceof Node) {
                            $oWrapper->addChild($sValue);
                        }
                    }
                }
                $this->addChild($oWrapper);
            }
        }

        //////////////////////////
        //  \IValuable

        /**
         * Return TRUE if value is not empty.
         *
         * @return bool
         */
        public function isEmpty()
        {
            foreach ($this->aRadioButtons as $oRadioButton) {
                if ($oRadioButton instanceof RadioButton && $oRadioButton->isChecked()) {
                    return false;
                }
            }
            return true;
        }

        /**
         * Sets value for IValuable objects. Should be able to take array or string.
         * Array values are for indexed FormElements. It can be passed from request.
         *
         * @param string $sValue
         */
        public function setValue($sValue)
        {
            foreach ($this->aRadioButtons as $oRadioButton) {
                if ($oRadioButton instanceof RadioButton) {
                    if ($oRadioButton->equals($sValue)) {
                        $oRadioButton->check();
                    } else {
                        $oRadioButton->uncheck();
                    }
                }
            }
        }

        /**
         * Returns value from IValuable object.
         *
         * @return string
         */
        public function getValue()
        {
            foreach ($this->aRadioButtons as $oRadioButton) {
                if ($oRadioButton instanceof RadioButton && $oRadioButton->isChecked()) {
                    return $oRadioButton->getValue();
                }
            }
            return null;
        }
    }
}