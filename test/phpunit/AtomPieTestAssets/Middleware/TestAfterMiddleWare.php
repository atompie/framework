<?php
namespace AtomPieTestAssets\Middleware {

    use AtomPie\Boundary\System\IRunAfterMiddleware;
    use AtomPie\Web\Boundary\IChangeResponse;
    use AtomPie\Web\Connection\Http\Content;

    class TestAfterMiddleWare implements IRunAfterMiddleware
    {

        /**
         * @param IChangeResponse $oResponse
         * @return IChangeResponse
         */
        public function after(IChangeResponse $oResponse)
        {
            $oResponse->setContent(new Content(TestAfterMiddleWare::class));
        }
    }

}
