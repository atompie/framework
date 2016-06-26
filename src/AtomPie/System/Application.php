<?php
namespace AtomPie\System {

    use AtomPie\EventBus\EventHandler;
    use AtomPie\DependencyInjection\DependencyContainer;
    use AtomPie\Boundary\Core\EventBus\IHandleEvents;
    use AtomPie\Boundary\Core\IAmFrameworkConfig;
    use AtomPie\Boundary\Core\IProcessContent;
    use AtomPie\Core\NamespaceHandler;
    use AtomPie\Boundary\System\IAmDispatcher;
    use AtomPie\Web\Boundary\IAmContent;
    use AtomPie\Web\Boundary\IAmContentType;
    use AtomPie\Web\Boundary\IAmEnvironment;
    use AtomPie\System\IO\File;
    use AtomPie\Web\Connection\Http\Request;
    use AtomPie\Web\Connection\Http\Response;
    use AtomPie\Web\Connection\Http\Url\Param;

    class Application implements IHandleEvents
    {

        const EVENT_BEFORE_PROCESSING = '@BeforeProcessing';
        const EVENT_BEFORE_RENDERING = '@BeforeRendering';
        const EVENT_BEFORE_RESPONDING = '@BeforeResponding';
        const EVENT_BEFORE_DISPATCH = '@BeforeDispatch';
        const EVENT_AFTER_END_POINT_INVOKE = '@AfterEndPointInvoke';

        use EventHandler;
        /**
         * @var DependencyContainer
         */
        private $oDependencyContainer;

        /**
         * @var IAmDispatcher
         */
        private $oDispatcher;

        /**
         * @var IAmEnvironment
         */
        private $oEnvironment;

        /**
         * @var IProcessContent
         */
        private $oContentProcessor;

        public function __construct(
            IAmDispatcher $oDispatcher,
            IAmEnvironment $oEnvironment,
            IProcessContent $oContentProcessor = null
        ) {

            $this->oEnvironment = $oEnvironment;
            $this->oDispatcher = $oDispatcher;
            $this->oContentProcessor = $oContentProcessor;

        }

        /**
         * @return DependencyContainer
         */
        public function getEndPointDependencyContainer()
        {
            if (isset($this->oDependencyContainer)) {
                return $this->oDependencyContainer;
            } else {
                return new DependencyContainer();
            }
        }

        public function injectDependency(DependencyContainer $oDependencyContainer)
        {
            $this->oDependencyContainer = $oDependencyContainer;
        }

        /**
         * @param $sEndPointClass
         * @param IAmFrameworkConfig $oConfig
         * @return string
         */
        private function prefixWithNamespace($sEndPointClass, $oConfig)
        {

            if (class_exists($sEndPointClass)) {
                return $sEndPointClass;
            }

            $aNamespaces = $oConfig->getEndPointNamespaces();
            $aClasses = $oConfig->getEndPointClasses();

            // Run available namespaces to find out class
            if (!empty($aNamespaces) || !empty($aClasses)) {

                $oNamespaceHandler = new NamespaceHandler(
                    $aNamespaces,
                    $aClasses
                );

                $sEndPointNamespace =
                    $oNamespaceHandler->getNamespaceForClass($sEndPointClass);


                return $sEndPointNamespace . '\\' . $sEndPointClass;

            }

            return $sEndPointClass;

        }

        /**
         * @param IAmFrameworkConfig $oConfig
         * @return Response
         * @throws \AtomPie\EventBus\Exception
         */
        public function run(IAmFrameworkConfig $oConfig)
        {

            $oEndPointSpec = $this->oDispatcher->getDispatchManifest()->getEndPoint();

            //////////////////////////////////
            // Constructing end-point object

            $sEndPointClass = $oEndPointSpec->getClassString();
            $sFullEndPointClassName = $this->prefixWithNamespace($sEndPointClass, $oConfig);

            //////////////////////////////////////
            // Returns EndPoint object it EndPoint
            // method is not static

            $mEndPointObject = $this->oDispatcher->getEndPointObject(
                $sFullEndPointClassName,
                $oConfig,
                $oEndPointSpec);

            $this->triggerEvent(self::EVENT_BEFORE_DISPATCH, $mEndPointObject);

            // Dispatch

            $mContent = $this->oDispatcher->dispatch(
                $mEndPointObject,
                $oConfig,
                $this->oEnvironment,
                $this->oContentProcessor,
                $this->getEndPointDependencyContainer()
            );

            $this->triggerEvent(self::EVENT_AFTER_END_POINT_INVOKE, $mContent);

            // Set response

            $oResponse = $this->oEnvironment->getResponse();
            $oContent = $oResponse->getContent();
            $oContentType = $oContent->getContentType();

            $this->setContent($oContent, $oContentType, $mContent);

            return $oResponse;
        }

        /**
         * @return IAmDispatcher
         */
        public function getDispatcher()
        {
            return $this->oDispatcher;
        }

        /**
         * @param IAmContent $oContent
         * @param IAmContentType $oContentType
         * @param $mContent
         * @throws \AtomPie\EventBus\Exception
         */
        private function setContent($oContent, $oContentType, $mContent)
        {

            // Set process and content

            $this->triggerEvent(self::EVENT_BEFORE_PROCESSING, $mContent);

            $mContent = $this->oContentProcessor->processAfter($mContent);

            $this->triggerEvent(self::EVENT_BEFORE_RENDERING, $mContent);

            $mContent = $this->oContentProcessor->processFinally($mContent, $oContentType);

            $this->triggerEvent(self::EVENT_BEFORE_RESPONDING, $mContent);

            $oContent->setContent($mContent);
        }

    }

}
