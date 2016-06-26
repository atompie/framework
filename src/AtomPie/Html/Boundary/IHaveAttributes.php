<?php
namespace AtomPie\Html\Boundary {

    use AtomPie\Html\Attribute;

    interface IHaveAttributes
    {
        /**
         * @param $oAttribute
         * @return \AtomPie\Html\ElementNode
         */
        public function addAttribute(Attribute $oAttribute);

        public function getAttribute($sName);

        public function removeAttribute($sName);

        public function hasAttribute($sName, $sNamespace = null);

        public function hasAttributes();
    }

}