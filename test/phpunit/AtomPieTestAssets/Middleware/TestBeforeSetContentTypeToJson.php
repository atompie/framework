<?php
namespace AtomPieTestAssets\Middleware {

    use AtomPie\Boundary\System\IRunBeforeMiddleware;
    use AtomPie\Web\Boundary\IChangeRequest;
    use AtomPie\Web\Boundary\IChangeResponse;

    class TestBeforeSetContentTypeToJson implements IRunBeforeMiddleware
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
            $oResponse->setContentType('application/json');
        }
    }

}
