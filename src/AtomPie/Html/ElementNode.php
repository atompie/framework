<?php
namespace AtomPie\Html {

    use AtomPie\Html\Boundary\IToStringWrap;
    use AtomPie\Html\Boundary\IHaveAttributes;

    class ElementNode extends TagNode implements IHaveAttributes
    {

        /**
         * Hold all attributes.
         *
         * @var \AtomPie\Html\Attributes
         */
        private $aAttributes;

        const ID = 'id';
        const NAME = 'name';

        public function __construct($sTagName, $sNamespace = null)
        {
            $this->aAttributes = new Attributes();
            parent::__construct($sTagName, $sNamespace);
        }

        ////////////////////////////
        // Html\IAttribute

        /**
         * @return \AtomPie\Html\Attributes
         */
        final public function getAttributes()
        {
            return $this->aAttributes;
        }

        /**
         * @param \AtomPie\Html\Attributes $oAttributes
         * @return void
         */
        final public function addAttributes(Attributes $oAttributes)
        {
            $this->aAttributes = $oAttributes;
        }

        /**
         * @param string $sName
         * @return \AtomPie\Html\Attribute
         */
        final public function getAttribute($sName)
        {
            return $this->aAttributes->getAttribute($sName);
        }

        /**
         * @param $sName
         */
        final public function removeAttribute($sName)
        {
            $this->aAttributes->removeAttribute($sName);
        }

        final public function hasAttribute($sName, $sNamespace = null)
        {
            return $this->aAttributes->hasAttribute($sName, $sNamespace);
        }

        /**
         * Glues attribute values. If there is attribute then new value
         * will be added. If there is no attribute then new one is added.
         *
         * @param Attribute $oAttribute
         */
        final public function mergeAttribute(Attribute $oAttribute)
        {
            if ($this->hasAttribute($oAttribute->getName())) {
                $this->getAttribute($oAttribute->getName())->addValue($oAttribute->getValue());
            } else {
                $this->addAttribute($oAttribute);
            }
        }

        final public function hasAttributes()
        {
            return $this->aAttributes->hasAttributes();
        }

        /**
         * (non-PHPdoc)
         * @see \Html\IAttribute
         * @param Attribute $oAttribute
         * @return $this|ElementNode
         */
        final public function addAttribute(Attribute $oAttribute)
        {
            $this->aAttributes->addAttribute($oAttribute);

            return $this;
        }

        // To string //////////////////////////////////////

        protected function beforeToString()
        {
        }

        protected function afterToString()
        {
        }

        final public function __toString()
        {

            if ($this instanceof Boundary\IToStringOverridable) {
                return $this->__overridedToString();
            }

            // Do to string
            $this->beforeToString();
            $sOutput = $this->render();
            $this->afterToString();

            if ($this instanceof IToStringWrap) {
                return $this->__wrapToString($sOutput);
            }

            return $sOutput;
        }

        /**
         * Returns opening tag string.
         *
         * @return string
         */
        protected function renderStart()
        {
            $sTag = ($this->aAttributes->hasAttributes())
                ? $this->getTagName() . ' ' . $this->aAttributes->__toString()
                : $this->getTagName();

            return $this->renderTag($sTag);
        }

        /**
         * Returns child tags and cdata nodes.
         *
         * @return string
         */
        protected function render()
        {
            if ($this->hasChildren()) {
                $this->bHasContent = true;
                $this->sTagClose = '>';
                return $this->renderStart() . implode('', $this->getChildren()) . $this->renderEnd();
            } else {
                if ($this->bShowCloseTagOnEmptyContent === false) {
                    $this->sTagClose = ' />';
                    $this->bHasContent = false;
                    return $this->renderStart();
                }
            }

            $this->bHasContent = false;
            return $this->renderStart() . $this->renderEnd();
        }

        /**
         * Returns close tag.
         *
         * @return string
         */
        private function renderEnd()
        {
            return $this->sTagOpen . '/' . $this->getTagName() . $this->sTagClose;
        }

        private function renderTag($sTagContent)
        {
            return $this->sTagOpen . $sTagContent . $this->sTagClose;
        }

    }
}