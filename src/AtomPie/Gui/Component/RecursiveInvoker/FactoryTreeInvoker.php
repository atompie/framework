<?php
namespace AtomPie\Gui\Component\RecursiveInvoker {

    use AtomPie\DependencyInjection\Boundary\IConstructInjection;
    use AtomPie\DependencyInjection\DependencyInjector;
    use AtomPie\Boundary\Gui\Component\IAmComponent;
    use AtomPie\Boundary\Gui\Component\ICanBeFactored;
    use AtomPie\Web\Connection\Http\Request;

    final class FactoryTreeInvoker
    {

        /**
         * Invokes all factories in sub nodes. Basically its an iterator.
         *
         * @param IConstructInjection $oComponentDependencyContainer
         * @param $mComponents
         * @param bool $bShipTopFactoryInTree
         */
        public function invokeFactoryTree(
            IConstructInjection $oComponentDependencyContainer,
            $mComponents,
            $bShipTopFactoryInTree = false
        ) {

            if ($mComponents instanceof IAmComponent) {

                $oComponent = $mComponents;
                if (!$bShipTopFactoryInTree) {
                    $this->invokeFactoryForComponent($oComponentDependencyContainer, $oComponent);
                }
                /** @var \AtomPie\Gui\Component\Part $oComponentPlaceHolder */
                if ($oComponent->hasPlaceHolders()) {
                    foreach ($oComponent->getPlaceHolders() as $oComponentPlaceHolder) {
                        $this->invokeFactoryTree($oComponentDependencyContainer, $oComponentPlaceHolder, false);
                    }
                }

            } else {
                if (is_array($mComponents)) {
                    foreach ($mComponents as $mComponentOrCollectionOfComponents) {
                        $this->invokeFactoryTree($oComponentDependencyContainer, $mComponentOrCollectionOfComponents,
                            false);
                    }
                }
            }

        }

        /**
         * Finds and invokes factory for $oComponent.
         * Factory by definition is placed in Factory folder above component class.
         *
         * @param IConstructInjection $oComponentDependencyContainer
         * @param ICanBeFactored $oComponent
         * @throws \AtomPie\DependencyInjection\Exception
         */
        public function invokeFactoryForComponent(
            IConstructInjection $oComponentDependencyContainer,
            ICanBeFactored $oComponent
        ) {

            // Skip already factored components
            if ($oComponent->isFactored()) {
                return;
            }

            // Mark so it will not run again
            $oComponent->markFactored();

            if (method_exists($oComponent, '__factory')) {
                $oInjector = new DependencyInjector($oComponentDependencyContainer);
                $oInjector->invokeMethod($oComponent, '__factory');
            }
        }

    }

}
