<?php
namespace AtomPie\Html {

    class CommentNode extends CharacterDataNode
    {

        public function __construct($sTextContent)
        {
            parent::__construct($sTextContent);
            $this->sTagOpen = '<!--';
            $this->sTagClose = '-->';
            $this->iNodeType = self::XML_COMMENT_NODE;
        }
    }
}