<?php
namespace AtomPie\Gui\Component {

    use AtomPie\Boundary\Core\Dispatch\IAmDispatchManifest;
    use AtomPie\Boundary\Core\IRegisterContentProcessors;
    use AtomPie\Boundary\Core\ISetUpDependencyContainer;
    use AtomPie\Boundary\Gui\Component\IAmComponent;
    use AtomPie\DependencyInjection\Dependency;
    use AtomPie\DependencyInjection\DependencyContainer;
    use AtomPie\Gui\Component\Template\Master;
    use AtomPie\Gui\Page;
    use AtomPie\Boundary\Core\ISetUpContentProcessor;
    use AtomPie\Boundary\Core\IAmFrameworkConfig;
    use AtomPie\Gui\ViewTree\ViewIterator;
    use AtomPie\View\Boundary\ICanBeRendered;
    use AtomPie\I18n\Label;
    use AtomPie\System\IO\File;
    use AtomPie\Web\Boundary\IAmContentType;
    use AtomPie\Web\Boundary\IAmEnvironment;
    use AtomPie\Web\Session\ParamStatePersister;

    class ComponentProcessorProvider implements ISetUpContentProcessor, ISetUpDependencyContainer
    {

        /**
         * @var IAmFrameworkConfig
         */
        private $oConfig;

        /**
         * @var IAmDispatchManifest
         */
        private $oDispatchManifest;

        /**
         * @var IAmEnvironment
         */
        private $oEnvironment;

        /**
         * @var ComponentDependencyContainer
         */
        private $oComponentDependencyContainer;

        public function __construct(
            IAmFrameworkConfig $oConfig,
            IAmEnvironment $oEnvironment
        ) {
            $this->oConfig = $oConfig;
            $this->oEnvironment = $oEnvironment;
        }

        /**
         * Runs before configureProcessor method.
         * Sets DispatchManifest.
         *
         * @param IAmDispatchManifest $oDispatchManifest
         */
        public function init(IAmDispatchManifest $oDispatchManifest)
        {
            $this->oDispatchManifest = $oDispatchManifest;
        }

        /**
         * @param IRegisterContentProcessors $oContentProcessor
         */
        public function configureProcessor(IRegisterContentProcessors $oContentProcessor)
        {

            $sRootFolder = $this->oConfig->getRootFolder();
            $sViewFolder = $this->oConfig->getViewFolder();

            $oComponentDependencyContainer = $this->oComponentDependencyContainer;

            $oContentProcessor->registerBefore(function () use ($oComponentDependencyContainer) {
                Part::injectDependency($oComponentDependencyContainer);
            });

            // !!! Order is important
            $oContentProcessor->registerAfter(
                Page::class,
                function (Page $oPage) use (
                    $oComponentDependencyContainer
                ) {

                    return ComponentService::buildPageTree(
                        $oPage,
                        $oComponentDependencyContainer, // Needed for Di injections
                        $this->oDispatchManifest  // Needed for events
                    );

                }
            );
            $oContentProcessor->registerAfter(
                IAmComponent::class,
                function (IAmComponent $oComponentAsContent) use (
                    $oComponentDependencyContainer // Needed for Di injections
                ) {

                    return ComponentService::buildComponentTree(
                        $oComponentDependencyContainer,
                        $oComponentAsContent,
                        $this->oDispatchManifest  // Needed for event
                    );

                });

            // !!! Order is important
            $oContentProcessor->registerFinally(
                Page::class,
                function (Page $oPage) use (
                    $sRootFolder,
                    $sViewFolder
                ) {
                    //////////////////////////////////////
                    // Render

                    $mContent = self::render(
                        ($sViewFolder !== null)
                            ? $sViewFolder
                            : $sRootFolder,
                        $oPage,
                        $this->oEnvironment->getResponse()->getContent()->getContentType()
                    );

                    // Wrap in template
                    if (is_string($mContent)) {
                        $oTemplate = new Master(
                            $mContent,
                            $oPage->getHeader()->getHeadTag(),
                            $oPage->getPageBottom()->getScriptCollection()
                        );
                        return $oTemplate->render();
                    }
                    return $mContent;
                }
            );
            $oContentProcessor->registerFinally(
                IAmComponent::class,
                function (IAmComponent $oComponent) use (
                    $sRootFolder,
                    $sViewFolder
                ) {
                    //////////////////////////////////////
                    // Render

                    return self::render(
                        ($sViewFolder !== null)
                            ? $sViewFolder
                            : $sRootFolder,
                        $oComponent,
                        $this->oEnvironment->getResponse()->getContent()->getContentType()
                    );
                }
            );

        }

        /**
         * @param $sTemplateFolder
         * @param ICanBeRendered $oComponent
         * @param IAmContentType $oContentType
         * @return mixed
         * @throws Exception
         */
        private static function render($sTemplateFolder, ICanBeRendered $oComponent, IAmContentType $oContentType)
        {

            if (!$oContentType->isHtml()) {
                throw new Exception(sprintf('Incorrect content-type in [%s]. Html renderer can render anything but HTML.',
                    get_class($oComponent)));
            }

            $oTemplateFile = $oComponent->getTemplateFile($sTemplateFolder);
            switch ($oTemplateFile->getExtension()) {
                case 'mustache':

                    $oMustache = new \Mustache_Engine();
                    $oIterator = new ViewIterator(
                        $sTemplateFolder,
                        function (File $oTemplateFile, $aPlaceHolders) use ($oMustache) {
                            return $oMustache->render($oTemplateFile->loadRaw(), $aPlaceHolders);
                        }
                    );
                    $mContent = $oIterator->renderComponent($oComponent);

                    break;

                default:
                    throw new Exception(new Label('Incorrect template extension!'));
            }

            return $mContent;

        }

        /**
         * @param \Closure[] $aDependencySet
         * @return ComponentDependencyContainer
         */
        public function initDependencyContainer($aDependencySet)
        {

            $oDependencyContainer = $this->factoryDependencyContainer();
            $oDependencyContainer = $this->mergeDependency($aDependencySet, $oDependencyContainer);

            $this->oComponentDependencyContainer = $oDependencyContainer;

        }

        /**
         * @param $aDependencySet
         * @param DependencyContainer $oDependencyContainer
         * @return DependencyContainer
         */
        private function mergeDependency($aDependencySet, $oDependencyContainer)
        {
            $oDependency = new Dependency();
            $oDependency->addDependency($aDependencySet);

            $oDependencyContainer
                ->forFunction(ComponentDependencyContainer::EVENT_CLOSURE_ID)
                ->merge($oDependency);
            $oDependencyContainer
                ->forAnyMethodInClass(Part::class)
                ->merge($oDependency);

            return $oDependencyContainer;
        }

        private function factoryDependencyContainer()
        {

            $oStatePersister = new ParamStatePersister(
                $this->oEnvironment->getSession(),
                $this->oDispatchManifest->getEndPoint()->__toString()
            );

            $oComponentDependencyContainer = new ComponentDependencyContainer(
                $this->oEnvironment,
                $this->oDispatchManifest,
                $oStatePersister
            );

            return $oComponentDependencyContainer;

        }

    }

}
