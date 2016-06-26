<?php
namespace AtomPie\Web\Boundary;

interface IAmContentType extends IRecognizeMediaType, IAmHttpHeader
{
    /**
     * @return string
     */
    public function getMediaType();

    public function getParam($sParamName);
}