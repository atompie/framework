<?php
namespace AtomPie\Web\Boundary;

interface IAmRequest extends
    IHaveHttpMethod,
    IHaveUrl,
    IHaveTimeOut,
    IHaveParams,
    IHaveHeaders,
    IHaveContent
{

    /**
     * Returns true if request is ajax or false if it is not.
     *
     * @return bool
     */
    public function isAjax();

    /**
     * Loads data from received request.
     *
     * @return $this
     */
    public function load();

    /**
     * @param $sVariableName
     * @return array|mixed|null
     * @throws \AtomPie\Web\Exception
     */
    public function getParamWithFallbackToBody($sVariableName);
}