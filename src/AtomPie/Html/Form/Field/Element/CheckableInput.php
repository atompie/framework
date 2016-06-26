<?php
namespace AtomPie\Html\Form\Field\Element {

    use AtomPie\Html\Attribute;
    use AtomPie\Html\Form\Field;
    use AtomPie\Html\Boundary\ICheckable;

    /**
     * Base class for checkboxes.
     *
     * @author risto
     */
    class CheckableInput extends Input implements ICheckable
    {

        ///////////////////////////
        // ICheckable

        /**
         * (non-PHPdoc)
         * @see ICheckable::check()
         */
        public function check()
        {
            $this->addAttribute(new Attribute('checked', 'checked'));
        }

        /**
         * (non-PHPdoc)
         * @see ICheckable::uncheck()
         */
        public function uncheck()
        {
            $this->removeAttribute('checked');
        }

        /**
         * (non-PHPdoc)
         * @see ICheckable::isChecked()
         */
        public function isChecked()
        {
            if ($this->hasAttribute('checked')) {
                $sChecked = $this->getAttribute('checked');
                return !empty($sChecked);
            }

            return false;
        }

        /**
         * Check element if value equals $sValue.
         * Unchecks if not.
         *
         * @param $sValue
         */
        public function checkValue($sValue)
        {
            if ($this->equals($sValue)) {
                $this->check();
            } else {
                $this->uncheck();
            }
        }

        /**
         * @param array|string $sValue
         * @return bool
         * @throws \AtomPie\Html\Exception
         */
        public function equals($sValue)
        {
            return $this->getValue() == $this->getIndexedValue($sValue);
        }
    }
}
