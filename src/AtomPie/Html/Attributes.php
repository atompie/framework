<?php
namespace AtomPie\Html {

    use AtomPie\Html\Boundary\IHaveAttributes;

    class Attributes implements \Iterator, \ArrayAccess, IHaveAttributes
    {

        private $aAttributes = array();

        //////////////////////
        // IHaveAttributes

        public function addAttribute(Attribute $oAttribute)
        {
            if ($oAttribute->hasNamespace()) {
                $this->aAttributes[$oAttribute->getNamespace() . ':' . $oAttribute->getName()] = $oAttribute;
            } else {
                $this->aAttributes[$oAttribute->getName()] = $oAttribute;
            }
        }

        public function removeAttribute($sName)
        {
            // None uft8 compliant
            unset($this->aAttributes[$sName]);
            unset($this->aAttributes[strtolower($sName)]);
        }

        public function hasAttributes()
        {
            return !empty($this->aAttributes);
        }

        /**
         * @param $sName
         * @return Attribute
         * @throws Exception
         */
        public function getAttribute($sName)
        {
            // None uft8 compliant
            if (!$this->hasAttribute($sName)) {
                throw new Exception('Attribute ' . $sName . ' is not defined! Caution: Attributes should always be in lower case.');
            }
            return $this->aAttributes[$sName];
        }

        public function hasAttribute($sName, $sNamespace = null)
        {
            // None uft8 compliant
            $sName = strtolower($sName);
            if (isset($sNamespace)) {
                return isset($this->aAttributes[$sNamespace . ':' . $sName]);
            } else {
                return isset($this->aAttributes[$sName]);
            }
        }

        public function __toString()
        {
            return implode(' ', $this->aAttributes);
        }

        ///////////////////////////
        // Iterator

        public function current()
        {
            return current($this->aAttributes);
        }

        public function next()
        {
            return next($this->aAttributes);
        }

        public function rewind()
        {
            return reset($this->aAttributes);
        }

        public function valid()
        {
            return array_key_exists($this->key(), $this->aAttributes);
        }

        public function key()
        {
            return key($this->aAttributes);
        }

        ///////////////////////////
        // ArrayAccess

        public function offsetExists($sAttributeName)
        {
            return isset($this->aAttributes[$sAttributeName]);
        }

        /**
         * @param $sAttributeName
         * @return Attribute
         */
        public function offsetGet($sAttributeName)
        {
            return $this->aAttributes[$sAttributeName];
        }

        public function offsetSet($sAttributeName, $oAttribute)
        {
            if (!$oAttribute instanceof Attribute) {
                throw new Exception('Only Attribute class available in Attributes.');
            }
            $this->aAttributes[$oAttribute->getName()] = $oAttribute;
        }

        public function offsetUnset($sAttributeName)
        {
            unset($this->aAttributes[$sAttributeName]);
        }

    }
}