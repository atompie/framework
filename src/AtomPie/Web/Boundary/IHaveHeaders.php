<?php
namespace AtomPie\Web\Boundary;

interface IHaveHeaders
{

    /**
     * Returns TRUE if has header $sName.
     *
     * @param string $sName
     * @return bool
     */
    public function hasHeader($sName);


    /**
     * Returns $sName header.
     * Remember to populate header values with load method.
     *
     * @param string $sName
     * @return IAmHttpHeader | boolean
     */
    public function getHeader($sName);

    /**
     * Returns array of headers (Web\Connection\Http\Header class).
     *
     * @return IAmHttpHeader[]
     */
    public function getHeaders();

    /**
     * Returns TRUE if header collection is not empty.
     *
     * @return bool
     */
    public function hasHeaders();
}