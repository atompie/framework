<?php
namespace AtomPie\EventBus {

    use AtomPie\DependencyInjection\Boundary\IConstructInjection;
    use AtomPie\DependencyInjection\DependencyInjector;

    /**
     *
     * Class responsible for handling events.
     *
     * Example:
     *
     * <code>
     * class Raiser {
     *
     *    use EventHandler;
     *
     *    function raise() {
     *        $this->triggerEvent('onTestEvent',array(1));
     *    }
     * }
     *
     * $oRaiser = new Raiser();
     *
     * // Closure
     * $oRaiser->handleEvent('onTestEvent',function($oObject, $aParams) { } );
     *
     * // Remove
     * $oRaiser->removeEventHandler('onTestEvent',$iEventId);
     *
     * $oRaiser->raise();
     * </code>
     *
     */
    trait EventHandler
    {

        /**
         * Holds evens and referenced listeners.
         *
         * @var array
         */
        private $aEventHandlers = array();
        /**
         * Event counter
         *
         * @var int
         */
        private $iEventCounter = 0;

        /**
         * @param $sEvent
         * @param array $aParams
         * @throws Exception
         */
        public function __invoke($sEvent, array $aParams = null)
        {
            if (empty($aParams)) {
                $aParams = [];
            }
            $this->triggerEvent($this->WHEN($sEvent), $aParams, $this);
        }

        private function WHEN($sName)
        {
            return 'When:' . $sName;
        }

        ///////////////////////////////
        // \IEventable

        /**
         * @param $sEvent
         * @param $pClosure
         * @return int
         * @throws Exception
         */
        final public function handleEvent($sEvent, \Closure $pClosure)
        {

            if (!is_string($sEvent)) {
                throw new Exception('Incorrect event name.');
            }

            $this->iEventCounter++;
            $this->aEventHandlers[$sEvent][$this->iEventCounter] = $pClosure;

            return $this->iEventCounter;
        }

        /**
         * @param $sEvent
         * @param null $iEventId
         * @throws Exception
         */
        final public function removeEventHandler($sEvent, $iEventId = null)
        {

            if (!is_string($sEvent)) {
                throw new Exception('Incorrect event name.');
            }

            if (isset($this->aEventHandlers[$sEvent])) {
                if (is_null($iEventId)) {
                    unset($this->aEventHandlers[$sEvent]);
                } else {
                    unset($this->aEventHandlers[$sEvent][$iEventId]);
                }
            }
        }

        /**
         * @param $sEvent
         * @param array|null $aParams
         * @param object|null $oSender
         * @throws Exception
         */
        final public function triggerEvent($sEvent, $aParams = null, $oSender = null)
        {

            if (!is_string($sEvent)) {
                throw new Exception('Incorrect event name.');
            }

            // Is there any listener?
            if ($this->hasEventHandler($sEvent)) {

                if (is_null($oSender)) {
                    $oSender = $this;
                }

                foreach ($this->getEventHandler($sEvent) as $pClosure) {
                    $pClosure($oSender, $aParams);
                }
            }
        }

        /**
         * @param string $sEvent
         * @return bool
         * @throws Exception
         */
        final public function hasEventHandler($sEvent)
        {

            if (!is_string($sEvent)) {
                throw new Exception('Incorrect event name.');
            }

            return isset($this->aEventHandlers[$sEvent]);
        }

        /**
         * @param $sEvent
         * @return array
         * @throws Exception
         */
        final public function getEventHandler($sEvent)
        {

            if (!is_string($sEvent)) {
                throw new Exception('Incorrect event name.');
            }

            return $this->aEventHandlers[$sEvent];
        }

        /**
         * @return boolean
         */
        final public function hasEventHandlers()
        {
            return !empty($this->aEventHandlers);
        }

        /**
         * @return array
         */
        final public function getEventHandlers()
        {
            return $this->aEventHandlers;
        }

        /**
         * @param IConstructInjection $oDependencyContainer
         * @param string $sClosureId
         * @param string $sEvent
         * @param array|null $aCustomDependency
         * @throws Exception
         */
        final public function triggerDependentEvent(
            IConstructInjection $oDependencyContainer,
            $sClosureId,
            $sEvent,
            array $aCustomDependency = null
        ) {

            if (!is_string($sEvent)) {
                throw new Exception('Incorrect event name.');
            }

            // Is there any listener?
            if ($this->hasEventHandler($sEvent)) {

                foreach ($this->getEventHandler($sEvent) as $pClosure) {

                    $oInjector = new DependencyInjector($oDependencyContainer);
                    $oInjector->invokeClosure($sClosureId, $pClosure, $aCustomDependency);

                }
            }
        }
    }
}
