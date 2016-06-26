<?php
namespace AtomPie\Gui\Component {

    use Generi\Object;
    use AtomPie\Boundary\Gui\Component\IHavePlaceHolders;
    use AtomPie\Html\Exception;

    class PlaceHolder extends Object implements IHavePlaceHolders
    {

        /**
         * @var array
         */
        private $aPlaceHolders = array();

        public function __set($sName, $mValue)
        {
            $this->aPlaceHolders[$sName] = $mValue;
        }

        public function &__get($sName)
        {
            if (isset($this->aPlaceHolders[$sName])) {
                return $this->aPlaceHolders[$sName];
            }

            $trace = debug_backtrace();
            throw new Exception(
                sprintf(
                    'Undefined property %s in %s on line %s', $sName, $trace[0]['file'], $trace[0]['line']
                )
            );

        }

        public function __isset($sName)
        {
            return isset($this->aPlaceHolders[$sName]);
        }

        public function __unset($sName)
        {
            unset($this->aPlaceHolders[$sName]);
        }

        /**
         * Merges array of properties with existing properties.
         *
         * @param array $aVariables
         */
        public function mergePlaceHolders(array $aVariables)
        {
            $this->aPlaceHolders = array_merge($this->getPlaceHolders(), $aVariables);
        }

        /**
         * @param array $aParams
         */
        public function setPlaceHolders($aParams)
        {
            $this->aPlaceHolders = $aParams;
        }

        /**
         * @return array
         */
        public function getPlaceHolders()
        {
            return $this->aPlaceHolders;
        }

        /**
         * @return bool
         */
        public function hasPlaceHolders()
        {
            return !empty($this->aPlaceHolders);
        }

    }

}