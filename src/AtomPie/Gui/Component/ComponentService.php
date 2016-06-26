<?php
namespace AtomPie\Gui\Component {

    use AtomPie\Boundary\Core\Dispatch\IHaveEventSpec;
    use AtomPie\DependencyInjection\Boundary\IConstructInjection;
    use AtomPie\Boundary\Gui\Component\IAmComponent;
    use AtomPie\Boundary\Gui\Component\IBasicAuthorize;
    use AtomPie\Boundary\Core\Dispatch\IAmDispatchManifest;
    use AtomPie\Core\Service\AuthorizeAnnotationService;
    use AtomPie\Gui\Component\RecursiveInvoker\EventTreeInvoker;
    use AtomPie\Gui\Component\RecursiveInvoker\BeforeEventInvokeParams;
    use AtomPie\Gui\Component;

    class ComponentService
    {

        public static function buildPageTree(
            IAmComponent $oComponent,
            IConstructInjection $oComponentDependencyContainer,
            IHaveEventSpec $oDispatchManifest
        ) {

            $oAnnotationHandler = new AuthorizeAnnotationService();

            $oComponentService = new ComponentService();

            $oTopComponent = $oComponentService->buildTreeOfComponents(
                $oComponentDependencyContainer,
                $oComponent,
                $oDispatchManifest, // Sent due to event tree
                $oAnnotationHandler
            );

            return $oTopComponent;
        }

        public static function buildComponentTree(
            IConstructInjection $oComponentDependencyContainer,
            IAmComponent $oComponent,
            IAmDispatchManifest $oDispatchManifest
        ) {

            $oAnnotationHandler = new AuthorizeAnnotationService();

            $oComponentService = new ComponentService();

            $oTopComponent = $oComponentService->buildTreeOfComponents(
                $oComponentDependencyContainer,
                $oComponent,
                $oDispatchManifest,
                $oAnnotationHandler
            );

            return $oTopComponent;
        }

        /**
         * @param IConstructInjection $oComponentDependencyContainer
         * @param IAmComponent $oComponent
         * @param IHaveEventSpec $oDispatchManifest
         * @param IBasicAuthorize $oAnnotationHandler
         * @return mixed
         */
        private function buildTreeOfComponents(
            IConstructInjection $oComponentDependencyContainer,
            IAmComponent $oComponent,
            IHaveEventSpec $oDispatchManifest, // required by event tree
            IBasicAuthorize $oAnnotationHandler
        ) {

            $oEventInvoker = new EventTreeInvoker();

            /** @noinspection PhpUnusedParameterInspection */
            $oEventInvoker->handleEvent(EventTreeInvoker::EVENT_BEFORE_INVOKE,
                function ($oSender, BeforeEventInvokeParams $oParams)
                use ($oAnnotationHandler) {
                    $oAnnotationHandler->checkAuthorizeAnnotation(
                        $oParams->getComponent(),
                        $oParams->getEvent());
                }
            );

            //////////////////////////////////////
            // Invoke __process tree

            $oFactoryInvoker = new RecursiveInvoker\FactoryTreeInvoker();
            $oProcessInvoker = new RecursiveInvoker\ProcessTreeInvoker();

            ///////////////////////////////////////////
            // Iterates processing.
            // Each invocation of __process may change
            // the component tree so the whole process
            // of tree building must be repeated.
            $this->iterateTreeProcessing(
                $oComponentDependencyContainer,
                $oComponent,
                $oDispatchManifest,
                $oProcessInvoker,
                $oFactoryInvoker,
                $oEventInvoker
            );

            return $oComponent;

        }

        /**
         * @param IConstructInjection $oComponentDependencyContainer
         * @param IAmComponent $oComponent
         * @param IHaveEventSpec $oDispatchManifest
         * @param \AtomPie\Gui\Component\RecursiveInvoker\ProcessTreeInvoker $oProcessInvoker
         * @param \AtomPie\Gui\Component\RecursiveInvoker\FactoryTreeInvoker $oFactoryInvoker
         * @param \AtomPie\Gui\Component\RecursiveInvoker\EventTreeInvoker $oEventInvoker
         */
        private function iterateTreeProcessing(
            $oComponentDependencyContainer,
            $oComponent,
            $oDispatchManifest,
            $oProcessInvoker,
            $oFactoryInvoker,
            $oEventInvoker
        ) {

            ///////////////////////
            // Factory
            $oFactoryInvoker->invokeFactoryTree($oComponentDependencyContainer, $oComponent);

            ///////////////////////
            // Events

            $oEventInvoker->invokeEventTree(
                $oComponentDependencyContainer,
                $oComponent,
                $oDispatchManifest
            );

            //////////////////////////////////////
            // Mark tree of components clean

            $oComponent->markTreeClean();

            ///////////////////////////////////////
            // Invoke __process tree

            $oProcessInvoker->invokeProcessTree($oComponentDependencyContainer, $oComponent);

            ///////////////////////////////////////
            // Iterate if not finished

            if ($oComponent->isTreeDirty()) {
                $this->iterateTreeProcessing(
                    $oComponentDependencyContainer,
                    $oComponent,
                    $oDispatchManifest,
                    $oProcessInvoker,
                    $oFactoryInvoker,
                    $oEventInvoker
                );
            };

        }
    }

}
