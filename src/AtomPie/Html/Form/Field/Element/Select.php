<?php
namespace AtomPie\Html\Form\Field\Element {

    use Generi\Boundary\ICollection;
    use AtomPie\Html\Boundary\IEnumberable;
    use AtomPie\Html\Attribute;
    use AtomPie\Html\Form\Field\Element;
    use AtomPie\Html\Exception;

    class Select extends Element implements IEnumberable
    {

        public $sId;
        public $sLabel;
        public $aData = array();

        private $aEnumerationSet = array();
        private $iNumberOfOptions = 0;

        public function __construct($sName)
        {
            parent::__construct('select', $sName);
            $this->closeTag();
        }

        /**
         * @param $sKey
         */
        public function setValue($sKey)
        {

            $sKey = $this->getIndexedValue($sKey);

            $aOptions = $this->getChildNodesByTagName('option');

            if (!empty($aOptions)) {
                foreach ($aOptions as $oOption) {
                    /** @var \AtomPie\Html\Form\Field\Element\SelectOption $oOption */
                    if ($oOption->getKey() == (string)$sKey) {
                        $oOption->setSelected();
                    } else {
                        $oOption->removeSelected();
                    }
                }
            }
        }

        /**
         * @return string
         */
        public function getValue()
        {
            $mResult = $this->getSelectedOption();
            if (is_null($mResult)) {
                return null;
            }
            /** @noinspection PhpUnusedLocalVariableInspection */
            list($iNumber, $oOption) = $mResult;
            if ($oOption instanceof Element\SelectOption) {
                return $oOption->getKey();
            }
            return null;
        }

        /**
         * @return bool
         */
        public function isEmpty()
        {
            $sValue = $this->getValue();
            return empty($sValue);
        }

        ////////////////////////////////
        // \IEnumberable

        /*
         * (non-PHPdoc) @see IEnumberable::setEnumeration()
        */
        public function setEnumeration($mEnumerationSet)
        {
            if ($mEnumerationSet instanceof ICollection) {
                $this->aEnumerationSet = $mEnumerationSet->getAll();
                $this->populateEnumeration();
            } else {
                if (is_array($mEnumerationSet)) {
                    $this->aEnumerationSet = $mEnumerationSet;
                    $this->populateEnumeration();
                } else {
                    throw new Exception('Can not set enumeration from other types then array or Collection.');
                }
            }
        }

        /*
         * (non-PHPdoc) @see IEnumberable::getEnumeration()
        */
        public function getEnumeration()
        {
            return $this->aEnumerationSet;
        }

        private function populateEnumeration()
        {
            foreach ($this->aEnumerationSet as $sKey => $sValue) {
                $this->addOption(new Element\SelectOption($sValue, $sKey));
            }
        }

        ////////////////////////////////

        public function bind($aData, $sLabelKey, $sValueKey, $sSelectedValue = null)
        {
            if (empty($aData)) {
                return;
            }
            foreach ($aData as $aRow) {
                $Label = isset($aRow[$sLabelKey]) ? $aRow[$sLabelKey] : 'not set';
                $sValue = isset($aRow[$sValueKey]) ? $aRow[$sValueKey] : 'not set';
                $oOption = new Element\SelectOption($sValue, $Label);
                if (!is_null($sSelectedValue) && $sValue == $sSelectedValue) {
                    $oOption->setSelected();
                }
                $this->addOption($oOption);
            }
        }

        public function bindMultiLabels($aData, $aLabelKeys, $sValueKey)
        {
            foreach ($aData as $aRow) {
                $aMultiLabels = Array();
                foreach ($aLabelKeys as $sLabelKey) {
                    $aMultiLabels[] = $aRow[$sLabelKey];
                }
                $this->addOption(new Element\SelectOption($aRow[$sValueKey], implode(' - ', $aMultiLabels)));
            }
        }

        ////////////////////////////////

        /**
         * @return array(int $iPostion,SelectOption $oOption);
         */
        public function getSelectedOption()
        {
            $aChildren = $this->getChildNodesByTagName('option');
            if (!empty($aChildren)) {
                $iNumber = 0;
                foreach ($aChildren as $oOption) {
                    if ($oOption instanceof Element\SelectOption) {
                        if ($oOption->isSelected()) {
                            return array($iNumber, $oOption);
                        }
                    }
                    $iNumber++;
                }
            }
            return null;
        }

        /**
         * @return bool;
         */
        public function hasSelectedOption()
        {
            $aChildren = $this->getChildNodesByTagName('option');
            foreach ($aChildren as $oOption) {
                if ($oOption instanceof Element\SelectOption) {
                    if ($oOption->isSelected()) {
                        return true;
                    }
                }
            }
            return false;
        }

        public function addOption(Element\SelectOption $oOption)
        {
            $this->addChild($oOption, $oOption->getKey());
            $this->iNumberOfOptions++;
        }

        public function getNumberOfOptions()
        {
            return $this->iNumberOfOptions;
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
            $this->addAttribute(new Attribute('name', $this->getName()));
            $this->addAttribute(new Attribute('id', $this->sId));
        }

    }
}
