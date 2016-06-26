<?php
namespace AtomPie\Web\Boundary;

interface IChangeHeaders extends IHaveHeaders
{

    public function resetHeaders();

    /**
     * Adds header to request.
     *
     * @param string $sName
     * @param string | IAmHttpHeader $sValue
     */
    public function addHeader($sName, $sValue);

    /**
     * @param string $sName
     */
    public function removeHeader($sName);

}