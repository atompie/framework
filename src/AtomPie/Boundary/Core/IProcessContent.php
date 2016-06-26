<?php
namespace AtomPie\Boundary\Core;

use AtomPie\Web\Boundary\IRecognizeMediaType;

/**
 * Interface IProcessContent in order to process content
 * returned form EndPoint.
 */
interface IProcessContent
{

    /**
     * @return mixed
     */

    public function processBefore();

    /**
     * Finds and invokes closure from closure repository.
     * Closures are indexed by content class type.
     * If $mContent is not an object it will not be
     * processed aby any closure.
     *
     * @param $mContent
     * @return mixed
     */
    public function processAfter($mContent);

    /**
     * Runs after the content is processed.
     *
     * @param $mContent
     * @param IRecognizeMediaType $oContentType
     * @return mixed
     */
    public function processFinally($mContent, IRecognizeMediaType $oContentType);
}