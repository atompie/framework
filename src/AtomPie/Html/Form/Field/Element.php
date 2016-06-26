<?php
namespace AtomPie\Html\Form\Field {

    use Generi\Boundary\IValuable;
    use AtomPie\Html\Boundary\IValuePopulator;
    use AtomPie\Html\Exception;

//	use AtomPie\Web\Connection\Http\Request;

    class Element extends AbstractElement implements IValuable, IValuePopulator
    {

        /**
         * @var string
         */
        protected $sValue;

        /////////////////////////
        // \IValuePopulator
        /**
         * Sets value for IMultiValues objects.
         *
         * @param array $aValues
         * @return void
         */
        public function __populateValue(array $aValues)
        {
            $sName = $this->getParamName()->getNameWithoutIndex();
            if (isset($aValues[$sName])) {
                $this->setValue($aValues[$sName]);
            }
        }

        ///////////////////////////
        // \IValuable

        /**
         * (non-PHPdoc)
         * @see \IValuable::isEmpty()
         */
        public function isEmpty()
        {
            return empty($this->sValue);
        }

        /**
         * @param $sValue
         * @throws Exception
         */
        public function setValue($sValue)
        {
            $this->sValue = $this->getIndexedValue($sValue);
        }

        /**
         * (non-PHPdoc)
         * @see \IValuable::getValue()
         */
        public function getValue()
        {
            return $this->sValue;
        }

        /////////////////////////////

        /**
         * @param $sValue
         * @return null|string
         * @throws Exception
         */
        protected function getIndexedValue($sValue)
        {
            // Some values from request can be arrays.
            // Check if it is an indexed form element.
            if (is_array($sValue)) {
                if ($this->hasIndex()) {
                    return isset($sValue[$this->getIndex()])
                        ? $sValue[$this->getIndex()]
                        : null;
                }
                throw new Exception('Incorrect value. Can\'t read array for not indexed form elements.');
            }

            if (!is_string($sValue)) {
                return (string)$sValue;
            }

            return $sValue;
        }

    }

}