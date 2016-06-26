<?php
namespace AtomPie\Core\Dispatch {

    use Generi\Type;
    use AtomPie\Boundary\Core\Dispatch\IAmEventSpecImmutable;
    use AtomPie\Core\NamespaceHandler;

    class EventSpecImmutable extends QueryString implements IAmEventSpecImmutable
    {

        const DEFAULT_SEPARATOR = '.';
        const EVENT_SUFFIX = 'Event';
        const EVENT_QUERY_OPEN_BRACKET = '{';
        const EVENT_QUERY_CLOSE_BRACKET = '}';

        private $sComponentClassType;
        private $sComponentName;
        private $sEvent;
        private $aComponentNamespaces;
        private $aComponentClasses;

        public function __construct(
            $sEventSpecString,
            array $aComponentNamespaces = null,
            array $aComponentClasses = null
        ) {

            $this->aComponentNamespaces = $aComponentNamespaces;
            $this->aComponentClasses = $aComponentClasses;

            $this->extractEventSpec($sEventSpecString);
        }

        /**
         * @param $sComponentType
         * @param $sComponentName
         * @param $sEvent
         * @param $aComponentNamespaces
         * @param $aComponentClasses
         * @return EventSpecImmutable
         */
        public static function factory(
            $sComponentType,
            $sComponentName,
            $sEvent,
            array $aComponentNamespaces = null,
            array $aComponentClasses = null
        ) {
            return new EventSpecImmutable(
                self::escape(trim($sComponentType, '\\')) . self::DEFAULT_SEPARATOR .
                $sComponentName . self::DEFAULT_SEPARATOR .
                $sEvent,
                $aComponentNamespaces,
                $aComponentClasses
            );
        }

        private function extractEventSpec($sEventSpecString)
        {

            $aExplodedSpecString = explode(self::DEFAULT_SEPARATOR, $sEventSpecString);
            if (count($aExplodedSpecString) != 3) {
                throw new EndPointException(sprintf('Event query message %s has unknown format!', $sEventSpecString));
            }

            list($sComponentClassType, $sComponentName, $sEvent) = $aExplodedSpecString;

            $this->sComponentClassType = self::escape($sComponentClassType);
            $this->sComponentName = self::escape($sComponentName);
            $this->sEvent = self::escape($sEvent);


            $oNamespaceHandler = new NamespaceHandler($this->aComponentNamespaces, $this->aComponentClasses);
            $sComponentClassName = $oNamespaceHandler->getFullClassName($this->sComponentClassType);

            if (null === $sComponentClassName) {
                if (!class_exists('\\' . $this->sComponentClassType)) {
                    throw new EndPointException(
                        sprintf(
                            'Component class %s could not be loaded! I looked for the class in %s, %s. Probable reason of this error is: The class may not exist or it\'s name is incorrect or it has not namespace defined. Check class namespace or registerAfter component namespace in config file. ',
                            $this->sComponentClassType,
                            implode(',', $this->aComponentNamespaces),
                            implode(',', $this->aComponentClasses)
                        )
                    );
                }
            } else {

                $this->sComponentClassType = $sComponentClassName;
            }

        }

        public function hasEvent()
        {
            return !empty($this->sEvent);
        }

        public function getComponentName()
        {
            return $this->sComponentName;
        }

        /**
         * @return Type
         */
        public function getComponentType()
        {
            return Type::getTypeOf($this->sComponentClassType);
        }

        public function getEvent()
        {
            return $this->sEvent;
        }

        public function getEventMethod()
        {
            return $this->sEvent . self::EVENT_SUFFIX;
        }

        public function __toString()
        {
            return $this->sComponentClassType . self::DEFAULT_SEPARATOR . $this->sComponentName . self::DEFAULT_SEPARATOR . $this->sEvent;
        }

        public function __toUrlString()
        {
            return
                self::EVENT_QUERY_OPEN_BRACKET .
                self::getEventSpecString(
                    $this->sComponentClassType,
                    $this->sComponentName,
                    $this->sEvent,
                    $this->aComponentNamespaces,
                    $this->aComponentClasses
                ) . self::EVENT_QUERY_CLOSE_BRACKET;
        }

        /**
         * Returns EventSpecString in form of
         * ClassType.Name.Event
         *
         * @param $sComponentClassName
         * @param $sComponentName
         * @param $sEvent
         * @param $aComponentNamespaces
         * @param $aComponentClasses
         * @return string
         */
        public static function getEventSpecString(
            $sComponentClassName,
            $sComponentName,
            $sEvent,
            $aComponentNamespaces,
            $aComponentClasses
        ) {
            $oShortener = new NamespaceHandler($aComponentNamespaces, $aComponentClasses);
            $sComponentClassName = $oShortener->shorten($sComponentClassName);
            return self::urlEscape($sComponentClassName) .
            self::DEFAULT_SEPARATOR .
            $sComponentName .
            self::DEFAULT_SEPARATOR .
            $sEvent;
        }

    }

}
