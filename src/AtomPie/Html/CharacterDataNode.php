<?php
namespace AtomPie\Html {

    class CharacterDataNode extends Node
    {

        public $sTextContent;

        public function __construct($sTextContent)
        {
            $this->sTextContent = $sTextContent;
            $this->iNodeType = self::XML_CDATA_SECTION_NODE;
            $this->sTagOpen = '<![CDATA[';
            $this->sTagClose = ']]>';
        }

        protected function render()
        {
            return $this->sTagOpen . $this->sTextContent . $this->sTagClose;
        }

        public function __toString()
        {
            return $this->render();
        }
    }
}