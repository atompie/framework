<?php
namespace AtomPieTestAssets\Middleware {

    use AtomPie\Boundary\System\IRunBeforeMiddleware;
    use AtomPie\Core\Dispatch\DispatchManifest;
    use AtomPie\Web\Boundary\IChangeRequest;
    use AtomPie\Web\Boundary\IChangeResponse;

    class TestBeforeMiddleWare implements IRunBeforeMiddleware
    {

        /**
         * Returns modified Request.
         *
         * @param IChangeRequest $oRequest
         * @param IChangeResponse $oResponse
         * @return IChangeRequest
         */
        public function before(IChangeRequest $oRequest, IChangeResponse $oResponse)
        {
            $oRequest->setParam(DispatchManifest::END_POINT_QUERY, 'MockEndPoint.getFile');
            $oResponse->addHeader('Custom', 'MyHeader');
        }
    }

}
