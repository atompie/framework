<?php
namespace AtomPie\Core\Dispatch {

    use Generi\Boundary\ICanBeIdentified;
    use AtomPie\Boundary\Core\Dispatch\IAmEndPointValue;
    use AtomPie\Boundary\Core\Dispatch\IAmEventSpecImmutable;
    use AtomPie\Boundary\Core\Dispatch\IAmDispatchManifest;
    use AtomPie\Boundary\Core\IAmFrameworkConfig;
    use AtomPie\Web\Boundary\IChangeParams;

    class DispatchManifest implements IAmDispatchManifest
    {

        const EVENT_QUERY = '__event';
        const END_POINT_QUERY = '__end_point';

        /**
         * @var EndPointImmutable
         */
        private $oEndPointQuery;

        /**
         * @var IAmEventSpecImmutable
         */
        private $oEventSpec = null;

        /**
         * @var IAmFrameworkConfig
         */
        private $oConfig;

        /**
         * DispatchManifest constructor.
         * @param IAmFrameworkConfig $oConfig
         * @param IAmEndPointValue $oEndPointSpec
         * @param IAmEventSpecImmutable|null $oEventSpec
         * @internal 
         */
        public function __construct(
            IAmFrameworkConfig $oConfig,
            IAmEndPointValue $oEndPointSpec,
            IAmEventSpecImmutable $oEventSpec = null
        ) {

            $this->oConfig = $oConfig;
            $this->oEndPointQuery = $oEndPointSpec;

            if ($oEventSpec !== null) {
                $this->oEventSpec = $oEventSpec;
            }

        }

        /**
         * @return \AtomPie\Core\Dispatch\EndPointImmutable
         */
        public function getEndPoint()
        {
            return $this->oEndPointQuery;
        }

        /**
         * @return bool
         */
        public function hasEventSpec()
        {
            return $this->oEventSpec !== null;
        }

        public function __toString()
        {
            $sComponentSpecString = '';
            $sEventSpec = '';
            if ($this->hasEventSpec()) {
                $sEventSpec = $this->getEventSpec()->__toUrlString();
            }

            return $this->getEndPoint()->cloneEndPointUrl()->getUrl() . $sComponentSpecString . $sEventSpec;
        }

        /**
         * @return \AtomPie\Core\Dispatch\EventSpecImmutable
         */
        public function getEventSpec()
        {
            return $this->oEventSpec;
        }

        ////////////////////////////////

        /**
         * @param IChangeParams $oRequest
         * @param \AtomPie\Boundary\Core\IAmFrameworkConfig $oConfig
         * @param string $sDefaultEndPointSpecString
         * @param string $sDefaultEventSpecString
         * @return DispatchManifest
         */
        public static function factory(
            IChangeParams $oRequest,
            IAmFrameworkConfig $oConfig,
            $sDefaultEndPointSpecString = 'Default.index',
            $sDefaultEventSpecString = null
        ) {

            $oDispatchManifest = new DispatchManifest(
                $oConfig,
                self::factoryEndPointSpec($oRequest, $oConfig->getEndPointNamespaces(), $oConfig->getEndPointClasses(),
                    $sDefaultEndPointSpecString),
                self::factoryEventSpec($oRequest, $oConfig->getEventNamespaces(), $oConfig->getEventClasses(),
                    $sDefaultEventSpecString)
            );

            ///////////////////////////////////////
            // Remove dispatch params from request

            $oRequest->removeParam(self::END_POINT_QUERY);
            $oRequest->removeParam(self::EVENT_QUERY);

            return $oDispatchManifest;

        }

        /**
         * @param $sComponentType
         * @param $sComponentName
         * @param $sEvent
         * @return \AtomPie\Core\\AtomPie\Core\Dispatch\EventSpecImmutable
         */
        public function newEventSpec(
            $sComponentType,
            $sComponentName,
            $sEvent
        ) {

            return EventSpecImmutable::factory($sComponentType,
                $sComponentName,
                $sEvent,
                $this->oConfig->getEventNamespaces(),
                $this->oConfig->getEventClasses()
            );
        }

        /**
         * @param IChangeParams $oRequest
         * @param $aComponentNamespaces
         * @param $aComponentClasses
         * @param string|null $sDefaultEventSpecString
         * @return null|\AtomPie\Core\Dispatch\EventSpecImmutable
         * @throws \AtomPie\Web\Exception
         */
        private static function factoryEventSpec(
            IChangeParams $oRequest,
            array $aComponentNamespaces = null,
            array $aComponentClasses = null,
            $sDefaultEventSpecString = null
        ) {

            $oEventSpec = null;
            if ($oRequest->hasParam(self::EVENT_QUERY)) {
                $oEventSpec = new EventSpecImmutable($oRequest->getParam(self::EVENT_QUERY), $aComponentNamespaces,
                    $aComponentClasses);
            } else {
                if (null !== $sDefaultEventSpecString) {
                    $oEventSpec = new EventSpecImmutable($sDefaultEventSpecString, $aComponentNamespaces,
                        $aComponentClasses);
                }
            }

            return $oEventSpec;

        }

        /**
         * @param IChangeParams $oRequest
         * @param $aEndPointNamespaces
         * @param $aEndPointClasses
         * @param string $sDefaultQueryString
         * @return EndPointImmutable
         * @throws \AtomPie\Web\Exception
         */
        private static function factoryEndPointSpec(
            IChangeParams $oRequest,
            $aEndPointNamespaces,
            $aEndPointClasses,
            $sDefaultQueryString = 'Default.index'
        ) {
            if ($oRequest->hasParam(self::END_POINT_QUERY)) {
                $sEndPoint = $oRequest->getParam(self::END_POINT_QUERY);
                $oEndPointQuery = new EndPointImmutable($sEndPoint, $aEndPointNamespaces, $aEndPointClasses);
            } else {
                $oEndPointQuery = new EndPointImmutable($sDefaultQueryString, $aEndPointNamespaces, $aEndPointClasses);
            }
            return $oEndPointQuery;
        }

        /**
         * Returns immutable clone of DispatchManifest with new event.
         *
         * @param ICanBeIdentified $oComponent
         * @param $sEvent
         * @return DispatchManifest
         */
        public function cloneWithEvent(ICanBeIdentified $oComponent, $sEvent)
        {
            return new DispatchManifest(
                $this->oConfig,
                $this->getEndPoint(),
                EventSpecImmutable::factory(
                    $oComponent->getType()->getFullName(),
                    $oComponent->getName(),
                    $sEvent,
                    $this->oConfig->getEventNamespaces(),
                    $this->oConfig->getEventClasses()
                )
            );
        }

    }

}