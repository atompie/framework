<?php
namespace AtomPie\Gui\Component {

    use AtomPie\Boundary\Core\Dispatch\IAmDispatchManifest;
    use AtomPie\Boundary\Core\Dispatch\IAmEndPointUrl;
    use AtomPie\Boundary\Gui\Component\IProvideEventUrl;
    use AtomPie\Boundary\Gui\Component\IHaveEvents;

    class EventUrlProvider implements IProvideEventUrl
    {

        /**
         * @var IAmDispatchManifest
         */
        private $oDispatchManifest;

        /**
         * @var array
         */
        private $aRequestParams;

        public function __construct(
            IAmDispatchManifest $oDispatchManifest,
            array $aRequestParams
        ) {

            $this->oDispatchManifest = $oDispatchManifest;
            $this->aRequestParams = $aRequestParams;
        }

        /**
         * Returns event url within the context of current EndPoint.
         * This function is immutable and creates new EndPointEventUrl object.
         *
         * @param IHaveEvents $oComponent
         * @param $sEvent
         * @param array $aParams
         * @return EventUrl
         */
        public function getEventUrl(IHaveEvents $oComponent, $sEvent, array $aParams = null)
        {

            $oEndPoint = $this->oDispatchManifest->getEndPoint();
            $sEventSpec = $this->oDispatchManifest->newEventSpec(
                $oComponent->getType()->getFullName(),
                $oComponent->getName(),
                $sEvent);

            $oUrl = new EventUrl($oEndPoint->__toUrlString(), $sEventSpec->__toUrlString());
            $this->mergeParams($oUrl, $aParams);

            return $oUrl;
        }

        /**
         * Returns EndPointUrl.
         * This function is immutable and creates new EndPointEventUrl object.
         *
         * @return IAmEndPointUrl
         */
        public function getUrl()
        {
            return $this->oDispatchManifest->getEndPoint()->cloneEndPointUrl();
        }

        /**
         * @param \AtomPie\Boundary\Gui\Component\IAmEventUrl $oUrl
         * @param array $aParams
         */
        private function mergeParams($oUrl, array $aParams = null)
        {
            if ($aParams !== null) {
                if (!empty($this->aRequestParams)) {
                    $this->aRequestParams = array_merge($this->aRequestParams, $aParams);
                } else {
                    $this->aRequestParams = $aParams;
                }
            }

            if (!empty($this->aRequestParams)) {
                $oUrl->setParams($this->aRequestParams);
            }
        }
    }

}
