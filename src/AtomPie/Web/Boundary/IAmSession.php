<?php
namespace AtomPie\Web\Boundary;

use Generi\Boundary\IAmKeyValueStore;

interface IAmSession extends IAmKeyValueStore, \Countable
{

    // TODO add constructor to interface

    public function mergeKeyValue($sKey, $mValue);

    /**
     * @return bool
     */
    public function isEmpty();

    /**
     * @return array
     */
    public function getAll();

}