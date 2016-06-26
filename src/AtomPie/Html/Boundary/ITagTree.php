<?php
namespace AtomPie\Html\Boundary;

use AtomPie\Html\Node;

interface ITagTree
{
    public function addChild(Node $oNode, $sKey = null);

    public function getChildren();

    public function hasChildren();

    /**
     * @param string
     * @param string
     * @return \AtomPie\Html\Node
     */
    public function hasChild($sKey, $sNamespace = null);

    public function removeChild($iNodeNumber, $sNamespace = null);
}