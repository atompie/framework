<?php
namespace AtomPie\System;

class EventConfig
{
    /**
     * @var Namespaces
     */
    private $aEventNamespaces;
    /**
     * @var Namespaces
     */
    private $aEventClasses;

    public function __construct(Namespaces $aEventNamespaces = null, Namespaces $aEventClasses = null)
    {
        $this->aEventNamespaces = ($aEventNamespaces !== null) ? $aEventNamespaces : new Namespaces();
        $this->aEventClasses = ($aEventClasses !==null) ? $aEventClasses : new Namespaces();
    }

    /**
     * @return Namespaces
     */
    public function getEventNamespaces()
    {
        return $this->aEventNamespaces;
    }

    /**
     * @return Namespaces
     */
    public function getEventClasses()
    {
        return $this->aEventClasses;
    }
}