<?php
namespace AtomPie\Gui\Component\RecursiveInvoker {

    use AtomPie\Boundary\Core\EventBus\IHandleEvents;
    use AtomPie\Boundary\Core\Dispatch\IHaveComponentName;
    use AtomPie\Boundary\Gui\Component\IExtendEvents;
    use AtomPie\Boundary\Gui\Component\IHaveEvents;
    use AtomPie\Boundary\Core\Dispatch\IHaveEventSpec;
    use AtomPie\DependencyInjection\Boundary\IConstructInjection;
    use AtomPie\DependencyInjection\DependencyInjector;
    use AtomPie\EventBus\EventHandler;
    use AtomPie\Gui\Component\ComponentDependencyContainer;
    use AtomPie\Gui\Component\EventException;
    use AtomPie\Web\Connection\Http\Request;

    final class EventTreeInvoker
    {

        const EVENT_BEFORE_INVOKE = '@BeforeInvoke';
        use EventHandler;

        /**
         * @param IConstructInjection $oComponentDependencyContainer
         * @param IHaveEvents $oComponent
         * @param \AtomPie\Boundary\Core\Dispatch\IHaveEventSpec $oDispatchManifest
         */
        public function invokeEvent(
            IConstructInjection $oComponentDependencyContainer,
            IHaveEvents $oComponent,
            IHaveEventSpec $oDispatchManifest
        ) {

            // Run __beforeEventDispatch even if the event is not present
            if ($oComponent instanceof IExtendEvents) {
                $oComponent->__beforeEventInvoke();
            }

            $this->triggerRequestEvent(
                $oComponentDependencyContainer,
                $oComponent,
                $oDispatchManifest);

            // Run __afterEventInvoke even if the event is not present
            if ($oComponent instanceof IExtendEvents) {
                $oComponent->__afterEventInvoke();
            }

        }

        /**
         * Invoke all events in a component tree. Component will be marked if
         * its event was raised. So next time you invoke event tree it will be skipped.
         *
         * @param IConstructInjection $oComponentDependencyContainer
         * @param mixed $mComponents Collection of components or single component
         * @param IHaveEventSpec $oDispatchManifest
         */
        public function invokeEventTree(
            IConstructInjection $oComponentDependencyContainer,
            $mComponents,
            IHaveEventSpec $oDispatchManifest
        ) {

            if ($mComponents instanceof IHaveEvents) {
                $oComponent = $mComponents;
                // Components that raised its events must be skipped.
                if (!$oComponent->isEventRaised()) {
                    $this->invokeEvent(
                        $oComponentDependencyContainer,
                        $oComponent,
                        $oDispatchManifest
                    );
                }
                if ($oComponent->hasPlaceHolders()) {
                    foreach ($oComponent->getPlaceHolders() as $oPlaceHolder) {
                        $this->invokeEventTree(
                            $oComponentDependencyContainer,
                            $oPlaceHolder,
                            $oDispatchManifest
                        );
                    }
                }
            } else {
                if (is_array($mComponents)) {
                    foreach ($mComponents as $oComponent) {
                        $this->invokeEventTree(
                            $oComponentDependencyContainer,
                            $oComponent,
                            $oDispatchManifest
                        );
                    }
                }
            }
        }

        /**
         * @param IConstructInjection $oComponentDependencyContainer
         * @param IHaveEvents $oComponent
         * @param \AtomPie\Boundary\Core\Dispatch\IHaveEventSpec $oDispatchManifest
         * @return null
         * @throws EventException
         */
        private function triggerRequestEvent(

            IConstructInjection $oComponentDependencyContainer,
            IHaveEvents $oComponent,
            IHaveEventSpec $oDispatchManifest
        ) {

            if ($oDispatchManifest->hasEventSpec()) {

                $oEventSpec = $oDispatchManifest->getEventSpec();

                if (!$this->isForComponent($oComponent, $oEventSpec)) {
                    return null;
                }

                ///////////////////////////////
                // Do not raise events twice

                $oComponent->markEventRaised();

                //////////////////////////////
                // Component::methodEvent()

                // Run event within component
                // It invokes methods inside component.
                // Methods are suffixed with Event string.

                $this->runEvent(
                    $oComponentDependencyContainer,
                    $oComponent,
                    $oEventSpec->getEventMethod()
                );

                // Inform listeners about event
                if ($oComponent instanceof IHandleEvents) {
                    $oComponent->triggerDependentEvent(
                        $oComponentDependencyContainer,
                        ComponentDependencyContainer::EVENT_CLOSURE_ID,
                        $oEventSpec->getEvent()
                    );
                }

            }

        }

        /**
         * @param IHaveEvents $oComponent
         * @param IHaveComponentName $oEventSpec
         * @return bool
         * @throws EventException
         */
        private function isForComponent($oComponent, $oEventSpec)
        {

            if (!self::isCorrectClassType($oComponent->getType()->getFullName(),
                $oEventSpec->getComponentType()->getFullName())
            ) {
                return false;
            }

            if (!$oComponent->hasName()) {
                throw new EventException('Event can be only triggered on object with name set!');
            }

            if ($oComponent->getName() != $oEventSpec->getComponentName()) {
                return false;
            }

            return true;
        }

        /**
         * @param $sComponentType
         * @param $sClassStringToInvoke
         * @return bool
         */
        private static function isCorrectClassType($sComponentType, $sClassStringToInvoke)
        {

            $sType = trim($sClassStringToInvoke, '\\');
            $sFullName = trim($sComponentType, '\\');

            return $sType == $sFullName;
        }

        /**
         * Runs action. Throws \Workshop\Exception if action method is not defined or not
         * available in class.
         *
         * @param IConstructInjection $oComponentDependencyContainer
         * @param IHaveEvents $oComponent
         * @param $sEventMethod
         * @return mixed
         * @throws \AtomPie\DependencyInjection\Exception
         */
        private function runEvent($oComponentDependencyContainer, $oComponent, $sEventMethod)
        {

            if (method_exists($oComponent, $sEventMethod)) {

                // TODO Response is send due to BasicAuth. Maybe it can be done better

                $this->triggerEvent(
                    self::EVENT_BEFORE_INVOKE,
                    new BeforeEventInvokeParams($oComponent, $sEventMethod));

                // Before event
                if ($oComponent instanceof IExtendEvents) {
                    $oComponent->__beforeEvent();
                }

                $oInjector = new DependencyInjector($oComponentDependencyContainer);
                $mReturn = $oInjector->invokeMethod($oComponent, $sEventMethod);

                // After event
                if ($oComponent instanceof IExtendEvents) {
                    $oComponent->__afterEvent($mReturn);
                }

                return $mReturn;

            }

            return null;

        }

    }
}
