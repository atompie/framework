<?php
namespace AtomPie\Web\Boundary;

interface IAmStatusHeader extends IAmHeader
{

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @return string
     */
    public function getVersion();

    /**
     * @param $iStatusCode
     * @return bool
     */
    public function is($iStatusCode);

    /**
     * @return bool
     */
    public function isServerError();

    /**
     * @return bool
     */
    public function isResourceError();

    /**
     * @return bool
     */
    public function isRedirect();

    /**
     * @return bool
     */
    public function isOk();
}