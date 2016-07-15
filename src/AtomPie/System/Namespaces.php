<?php
namespace AtomPie\System;

class Namespaces
{

    /**
     * @var array
     */
    private $aNamespaces= [];

    public function __construct(array $aEndPointNamespaces= [])
    {

        $this->aNamespaces = $aEndPointNamespaces;
    }

    /**
     * @param $sNamespace
     * @return bool
     */
    public function hasEndPointNamespace($sNamespace) {
        return in_array($sNamespace, $this->aNamespaces);
    }

    /**
     * @param $sNamespace
     */
    public function prependEndPointNamespace($sNamespace) {
        array_unshift(
            $this->aNamespaces,
            $sNamespace
        );
    }

    /**
     * @return array
     */
    public function __toArray() {
        return $this->aNamespaces;
    }
    
}