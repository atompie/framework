<?php
namespace AtomPie\Html\Boundary {

    /**
     * Implement on Select, Checkbox, CheckRadio or any enumerations.
     */
    interface IEnumberable
    {
        /**
         * Set enumeration.
         * Example:
         *
         * $this->setEnumeration(array('id'=>'value'));
         *
         * @param Ambigous <Array, Collection> $mEnumerationSet
         */
        public function setEnumeration($mEnumerationSet);

        /**
         * @return array
         */
        public function getEnumeration();
    }
}
