<?php
namespace AtomPie\Html {

    class TextNode extends CharacterDataNode
    {

        public function __construct($sTextContent)
        {
            parent::__construct($sTextContent);
            $this->sTagOpen = '';
            $this->sTagClose = '';
            $this->iNodeType = self::XML_CDATA_SECTION_NODE;
        }

        protected function render()
        {
            return parent::render();
        }
    }
}
