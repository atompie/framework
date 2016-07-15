<?php
namespace WorkshopTest {

    use AtomPie\Core\Dispatch\DispatchManifest;
    use AtomPie\Web\Connection\Http\Request;

    class RequestFactory
    {

        /**
         * @param string $sDefaultEndPointSpecString
         * @param null $sDefaultEventSpecString
         * @param array $aParams
         * @return Request
         */
        public static function produce(
            $sDefaultEndPointSpecString = 'Default.index',
            $sDefaultEventSpecString = null,
            array $aParams = null
        ) {

            $oEnvironment = Boot::getEnv();

            $_REQUEST[DispatchManifest::END_POINT_QUERY] = $sDefaultEndPointSpecString;
            if (isset($sDefaultEventSpecString)) {
                $_REQUEST[DispatchManifest::EVENT_QUERY] = $sDefaultEventSpecString;
            }

            if (is_array($aParams)) {
                foreach ($aParams as $sKey => $mValue) {
                    $_REQUEST[$sKey] = $mValue;
                }
            }

            $oRequest = $oEnvironment->getRequest();
            $oRequest->load();

            return $oRequest;

        }
    }

}
