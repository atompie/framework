<?php
namespace AtomPie\System;

class EndPointConfig
{
    /**
     * @var Namespaces
     */
    private $oEndPointNamespaces;
    /**
     * @var Namespaces
     */
    private $oEndPointClasses;

    public function __construct(Namespaces $oEndPointNamespaces, Namespaces $oEndPointClasses = null)
    {
        $this->oEndPointNamespaces = $oEndPointNamespaces;
        $this->oEndPointClasses = ($oEndPointClasses !== null) ? $oEndPointClasses : new Namespaces();
    }

    /**
     * @return Namespaces
     */
    public function getEndPointNamespaces()
    {
        return $this->oEndPointNamespaces;
    }

    /**
     * @return Namespaces
     */
    public function getEndPointClasses()
    {
        return $this->oEndPointClasses;
    }


}