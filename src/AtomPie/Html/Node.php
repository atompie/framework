<?php
namespace AtomPie\Html {

    use Generi\Boundary\IStringable;

    abstract class Node implements IStringable
    {
        /**
         * Holds node type.
         *
         * @var int
         */
        public $iNodeType;

        const XML_ELEMENT_NODE = 1;
        const XML_ATTRIBUTE_NODE = 2;
        const XML_TEXT_NODE = 3;
        const XML_CDATA_SECTION_NODE = 4;
        const XML_ENTITY_REF_NODE = 5;
        const XML_PI_NODE = 7;
        const XML_COMMENT_NODE = 8;
        const XML_DOCUMENT_NODE = 9;
        const XML_DOCUMENT_TYPE_NODE = 10;
        const XML_DOCUMENT_FRAG_NODE = 11;
        const XML_NOTATION_NODE = 12;
        const XML_HTML_DOCUMENT_NODE = 13;

        /**
         * Node open character.
         *
         * @var string
         */
        protected $sTagOpen;
        /**
         * Node close character.
         *
         * @var string
         */
        protected $sTagClose;

    }
}