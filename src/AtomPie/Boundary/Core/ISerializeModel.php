<?php
namespace AtomPie\Boundary\Core;

interface ISerializeModel
{
    /**
     * Returns array with model data to be serialized
     * during json or XML serialization.
     *
     * @return array
     */
    public function __toModel();
}