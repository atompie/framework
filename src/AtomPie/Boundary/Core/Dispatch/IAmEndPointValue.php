<?php
namespace AtomPie\Boundary\Core\Dispatch;

interface IAmEndPointValue extends IHaveAccessToEndPoint
{
    /**
     * @return string
     */
    public function getClassString();

    /**
     * @return string
     */
    public function getMethodString();

    /**
     * @return bool
     */
    public function isDefaultMethod();

    /**
     * @return bool
     */
    public function hasMethod();

    /**
     * @return string
     */
    public function __toString();

    public function __toUrlString();

}