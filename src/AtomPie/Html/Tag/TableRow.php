<?php
namespace AtomPie\Html\Tag {

    use AtomPie\Html\ElementNode;

    class TableRow extends ElementNode
    {

        const ROW_RENDER_EVENT = '__TableRowRender__';

        public function __construct()
        {
            parent::__construct('tr');
        }
    }

}