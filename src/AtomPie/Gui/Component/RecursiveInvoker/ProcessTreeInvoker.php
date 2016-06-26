<?php
namespace AtomPie\Gui\Component\RecursiveInvoker {

    use AtomPie\DependencyInjection\Boundary\IConstructInjection;
    use AtomPie\DependencyInjection\DependencyInjector;
    use AtomPie\Boundary\Gui\Component\ICanBeProcessed;
    use AtomPie\Boundary\Gui\Component\IHavePlaceHolders;
    use AtomPie\Web\Connection\Http\Request;

    final class ProcessTreeInvoker
    {

        /**
         * @param $oComponentDependencyContainer
         * @param ICanBeProcessed $oComponent
         * @throws \AtomPie\DependencyInjection\Exception
         */
        public function invokeProcess(IConstructInjection $oComponentDependencyContainer, ICanBeProcessed $oComponent)
        {

            // Skip processed

            if ($oComponent->isProcessed()) {
                return;
            }

            $oComponent->markProcessed();

            // Call __process
            if (method_exists($oComponent, '__process')) {
                $oInjector = new DependencyInjector($oComponentDependencyContainer);
                $oInjector->invokeMethod($oComponent, '__process');
            }
        }

        /**
         * Invoke all events in a component tree.
         *
         * @param IConstructInjection $oComponentDependencyContainer
         * @param mixed $mComponents Collection of components or single component
         */
        public function invokeProcessTree(IConstructInjection $oComponentDependencyContainer, $mComponents)
        {

            if ($mComponents instanceof IHavePlaceHolders) {
                $oComponent = $mComponents;
                if ($oComponent instanceof ICanBeProcessed) {
                    $this->invokeProcess($oComponentDependencyContainer, $oComponent);
                }
                if ($oComponent->hasPlaceHolders()) {
                    foreach ($oComponent->getPlaceHolders() as $oPlaceHolder) {
                        $this->invokeProcessTree($oComponentDependencyContainer, $oPlaceHolder);
                    }
                }
            } else {
                if (is_array($mComponents)) {
                    foreach ($mComponents as $oComponent) {
                        $this->invokeProcessTree($oComponentDependencyContainer, $oComponent);
                    }
                }
            }
        }
    }
}
