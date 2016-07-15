<?php
namespace AtomPie\System {

    use AtomPie\Core\Dispatch\EndPointImmutable;
    use AtomPie\DependencyInjection\DependencyInjector;
    use AtomPie\DependencyInjection\Boundary\IConstructInjection;
    use AtomPie\Boundary\Core\Dispatch\IAmEndPointValue;
    use AtomPie\Boundary\Core\Dispatch\IAmDispatchManifest;
    use AtomPie\Boundary\Core\IProcessContent;
    use AtomPie\Core\Service\AuthorizeAnnotationService;
    use AtomPie\Boundary\Core\IAmFrameworkConfig;
    use AtomPie\DependencyInjection\Exception;
    use AtomPie\System\Dispatch\ClientAnnotationValidator;
    use AtomPie\System\Dispatch\DispatchException;
    use AtomPie\AnnotationTag\Client;
    use AtomPie\Boundary\System\IAmDispatcher;
    use AtomPie\Boundary\System\IControlAccess;
    use AtomPie\System\Dispatch\DispatchAnnotationFetcher;
    use Generi\Boundary\IType;
    use AtomPie\Web\Boundary\IAmEnvironment;
    use AtomPie\Web\Boundary\IChangeRequest;
    use AtomPie\AnnotationTag\EndPoint;
    use AtomPie\I18n\Label;
    use AtomPie\System\IO\File;
    use AtomPie\Web\Boundary\IChangeResponse;
    use AtomPie\Web\Connection\Http\Header\Status;

    /**
     * MVC controller. Dispatch service is responsible for dispatching to classes.method controller.
     *
     * Dependencies IAccessController
     *
     * @version $Id$
     * @author Risto Kowaczewski
     * @internal
     */
    class Dispatcher implements IAmDispatcher
    {

        const ON_AFTER_ENDPOINT_CALL = '@OnAfterEndPointCall';
        const ON_BEFORE_RESPONSE_FILL = '@OnBeforeResponseFill';

        /**
         * @var Dispatcher
         */
        protected static $oInstance;

        /**
         * @var IAmDispatchManifest
         */
        private $oDispatchManifest;

        /**
         * @param IAmDispatchManifest $oDispatchManifest
         */
        public function __construct(
            IAmDispatchManifest $oDispatchManifest
        ) {
            $this->oDispatchManifest = $oDispatchManifest;
        }

        /**
         * Checks whether there is an request event pending. May throw an Exception if
         * Kernel is not booted.
         *
         * @param IType $oComponent
         * @param $sComponentName
         * @param $sEvent
         * @return bool
         */
        public function hasRequestEvent(IType $oComponent, $sComponentName, $sEvent)
        {
            $oDispatcherManifest = $this->getDispatchManifest();
            if ($oDispatcherManifest->hasEventSpec()) {
                $oEventSpec = $oDispatcherManifest->getEventSpec();
                if ($oEventSpec->hasEvent()) {
                    return
                        strtolower($oEventSpec->getEvent()) == strtolower($sEvent)
                        && $oComponent->getFullName() == $oEventSpec->getComponentType()
                        && $sComponentName == $oEventSpec->getComponentName();
                }
            }
            return false;
        }

        /////////////////////
        // Getters / Setters

        /**
         * @return \AtomPie\Core\Dispatch\DispatchManifest
         */
        public function getDispatchManifest()
        {
            return $this->oDispatchManifest;
        }

        private function setResponseHeaders(IChangeResponse $oResponse, $aAnnotations)
        {

            /** @var \AtomPie\AnnotationTag\Header $oAnnotation */
            foreach ($aAnnotations as $oAnnotation) {

                if (isset($oAnnotation->ContentType)) {
                    $oResponse->setContentType($oAnnotation->ContentType);
                }

                if (isset($oAnnotation->CacheControl)) {
                    $oResponse->addHeader('Cache-Control', $oAnnotation->CacheControl);
                }

                if (isset($oAnnotation->ContentDisposition)) {
                    $oResponse->addHeader('Content-Disposition', $oAnnotation->ContentDisposition);
                }

                if (isset($oAnnotation->ContentEncoding)) {
                    $oResponse->addHeader('Content-Encoding', $oAnnotation->ContentEncoding);
                }

                if (isset($oAnnotation->Date)) {
                    $oResponse->addHeader('Date', $oAnnotation->Date);
                }

                if (isset($oAnnotation->Expires)) {
                    $oResponse->addHeader('Expires', $oAnnotation->Expires);
                }

                if (isset($oAnnotation->Server)) {
                    $oResponse->addHeader('Server', $oAnnotation->Server);
                }
            }
        }

        ///////////////////////////////

        /**
         * Dispatch. Runs controller and afterward action.
         *
         * @param $oEndPointObject
         * @param IAmFrameworkConfig $oConfig
         * @param IAmEnvironment $oEnvironment
         * @param \AtomPie\Boundary\Core\IProcessContent $oContentProcessor
         * @param IConstructInjection $oEndPointDependencyContainer
         * @return object
         * @throws DispatchException
         * @throws Exception
         * @throws \Generi\Exception
         */
        public function dispatch(
            $oEndPointObject, // object or class name
            IAmFrameworkConfig $oConfig,
            IAmEnvironment $oEnvironment,
            IProcessContent $oContentProcessor,
            IConstructInjection $oEndPointDependencyContainer
        ) {

            $oEndPointSpec = $this->getDispatchManifest()->getEndPoint();

            if (is_object($oEndPointObject)) {
                $sFullEndPointClassName = get_class($oEndPointObject);
            } else {
                $sFullEndPointClassName = $oEndPointObject;
            }

            ///////////////////////////////////////////////////
            // EndPoint annotations Authorize
            // On class level

            $oAnnotationHandler = new DispatchAnnotationFetcher();
            $oAuthorizeAnnotationHandler = new AuthorizeAnnotationService();
            // Can END dispatch if not authorized

            $oResponse = $oEnvironment->getResponse();
            $oRequest = $oEnvironment->getRequest();

            $oAuthorizeAnnotationHandler->checkAuthorizeAnnotation(
                $oEndPointObject
            );   // From class

            ///////////////////////////////////////////
            // Set headers annotated in EndPoint class

            $aHeaderAnnotations = $oAnnotationHandler->getHeaderAnnotation($sFullEndPointClassName); // From class
            if ($aHeaderAnnotations !== null) {
                $this->setResponseHeaders($oResponse, $aHeaderAnnotations);
            }

            ////////////////////////////////////////
            // @EndPoint annotation on class level

            if ($oEndPointSpec->isDefaultMethod() && !method_exists($oEndPointObject,
                    EndPointImmutable::DEFAULT_METHOD)
            ) {

                // Get @EndPoint from class

                $oEndPointAnnotation = $oAnnotationHandler->getEndPointClassAnnotation($oEndPointObject);
                if (null === $oEndPointAnnotation) {
                    throw new DispatchException(
                        sprintf(
                            new Label('Class [%s] can not be invoked. Please provide @EndPoint annotation'),
                            $oEndPointSpec->getClassString()
                        ),
                        Status::NOT_FOUND
                    );
                }

                if (!empty($oEndPointAnnotation->ContentType)) {
                    $oResponse->setContentType($oEndPointAnnotation->ContentType);
                }

                $this->validateClient(
                    $oRequest, $oAnnotationHandler->getClientAnnotation($sFullEndPointClassName)
                );

                if (is_object($oEndPointObject)) {
                    return $oEndPointObject;
                }

                return new $oEndPointObject();

            }

            //////////////////////////////////////
            // Get EndPoint Method

            $sEndPointMethodName = $this->getEndPointMethodName(
                $sFullEndPointClassName,
                $oEndPointSpec
            );

            ///////////////////////////////
            // EndPoint PHPDOC annotations
            // @EndPoint

            $oEndPointAnnotation = $oAnnotationHandler->getEndPointAnnotation(
                $sFullEndPointClassName,
                $sEndPointMethodName);

            if (null === $oEndPointAnnotation) {
                throw new DispatchException(
                    sprintf(
                        new Label('Method [%s] in class [%s] can not be invoked. Please provide @EndPoint annotation'),
                        $sEndPointMethodName,
                        $sFullEndPointClassName
                    ),
                    Status::NOT_FOUND
                );
            }

            // EndPoint Content-Type
            // TODO Also set content type

            if ($oEndPointAnnotation instanceof EndPoint) {
                if (!empty($oEndPointAnnotation->ContentType)) {
                    $oResponse->setContentType($oEndPointAnnotation->ContentType);
                }
            }

            /////////////////////////////////
            // Validate client expectations
            // @Client

            $this->validateClient(
                $oRequest, $oAnnotationHandler->getClientAnnotation($sFullEndPointClassName, $sEndPointMethodName)
            );

            // TODO remove - no need for header tag on method level
            // TODO Use DI
            // Throws Exception
            $aHeaderAnnotations = $oAnnotationHandler->getHeaderAnnotation($sFullEndPointClassName,
                $sEndPointMethodName); // From method

            if ($aHeaderAnnotations !== null) {
                $this->setResponseHeaders($oResponse, $aHeaderAnnotations);
            }

            ///////////////////////////////////////////////////
            // EndPoint annotations Authorize
            // On method level

            $oAuthorizeAnnotationHandler->checkAuthorizeAnnotation(
                $oEndPointObject,
                $sEndPointMethodName
            ); // From method

            ////////////////////////////////////////
            // Method level dependency container

            if(method_exists($oEndPointObject, '__dependency')) {

                    $aInjectedDependenciesDefinition = $oEndPointObject->__dependency();

                    // TODO musi rozbudowac interface.
    
                    /**
                     * @var $oEndPointDependencyContainer \AtomPie\DependencyInjection\DependencyContainer
                     */
                    foreach ($aInjectedDependenciesDefinition as $sMethodConstrain=>$aMethodDependencies) {
                        // Appends dependencies from method dependency container
                        $oEndPointDependencyContainer
                            ->forMethod($sFullEndPointClassName, $sMethodConstrain)
                            ->addDependency($aMethodDependencies);                
                    }
                
            }
            
            

            ////////////////////////////
            // Invoke

            return $this->invokeEndPoint(
                $oEndPointDependencyContainer,
                $oEndPointObject,
                $sFullEndPointClassName,
                $sEndPointMethodName,
                $oConfig->getContractsFillers()
            );

        }

        /**
         * @param $sNameSpacedEndPointClass
         * @return object
         * @throws DispatchException
         */
        private function factorEndPointObject($sNameSpacedEndPointClass)
        {

            $oEndPointClass = new $sNameSpacedEndPointClass();

            if ($oEndPointClass instanceof IControlAccess) {
                if (true !== $oEndPointClass->authorize()) {
                    $oEndPointClass->invokeNotAuthorized();
                }
            }

            return $oEndPointClass;
        }

        /**
         * @param IConstructInjection $oEndPointDependencyContainer
         * @param object $oEndPointClass
         * @param $sFullEndPointClassName
         * @param $sEndPointMethodName
         * @param ContractFillers $oContactFillers
         * @return mixed $ReturnContent
         * @throws Exception
         */
        private function invokeEndPoint(
            $oEndPointDependencyContainer,
            $oEndPointClass = null,
            $sFullEndPointClassName,
            $sEndPointMethodName,
            ContractFillers $oContactFillers = null
        ) {

            /////////////////////////////////////
            // Invoke EndPoint action
            // and return content
            
            $oDependencyInjector = new DependencyInjector($oEndPointDependencyContainer, $oContactFillers);
            $mContentReturnedByEndPoint = $oDependencyInjector->invokeMethod(
                is_object($oEndPointClass)
                    ? $oEndPointClass           // object method
                    : $sFullEndPointClassName,  // static method
                $sEndPointMethodName
            );

            return $mContentReturnedByEndPoint;

        }

        /**
         * @param $sFullEndPointClassName
         * @param IAmEndPointValue $oEndPointSpec
         * @return string
         * @throws DispatchException
         */
        private function getEndPointMethodName(
            $sFullEndPointClassName,
            IAmEndPointValue $oEndPointSpec
        ) {

            if (!$oEndPointSpec->hasMethod()) {
                throw new DispatchException(
                    sprintf(
                        new Label('Empty EndPoint name for class [%s]. Can not invoke endpoint.'),
                        $oEndPointSpec->getClassString()
                    ),
                    Status::NOT_FOUND
                );
            }

            /////////////////////////////////////////////////
            // At this point method must existsExactly in object

            $sEndPointMethodString = $oEndPointSpec->getMethodString();

            if (!method_exists($sFullEndPointClassName, $sEndPointMethodString)) {
                throw new DispatchException(
                    sprintf(
                        new Label('Can not invoke endpoint [%s.%s]. Method may not exist.'),
                        $sFullEndPointClassName,
                        $sEndPointMethodString
                    ),
                    Status::NOT_FOUND
                );
            }

            return $sEndPointMethodString;

        }

        /**
         * Returns EndPoint object it EndPoint method is not static
         *
         * @param $sEndPointFullClassName
         * @param \AtomPie\Boundary\Core\IAmFrameworkConfig $oConfig
         * @param IAmEndPointValue $oEndPointSpec
         * @return string|object
         * @throws DispatchException
         */
        public function getEndPointObject(
            $sEndPointFullClassName,
            IAmFrameworkConfig $oConfig,
            IAmEndPointValue $oEndPointSpec
        ) {

            ///////////////////////////////////////////
            // Throw exception if class does not exist

            $this->checkIfClassExists($sEndPointFullClassName, $oConfig);

            $sMethodString = $oEndPointSpec->getMethodString();

            if (method_exists($sEndPointFullClassName, $sMethodString)) {

                $oReflectionMethod = new \ReflectionMethod($sEndPointFullClassName, $sMethodString);

                // Static
                if ($oReflectionMethod->isStatic()) {
                    return $sEndPointFullClassName;
                }

                return $this->factorEndPointObject($sEndPointFullClassName);
            }
            return $sEndPointFullClassName;

        }

        /**
         * @param $sFullEndPointClassName
         * @param \AtomPie\Boundary\Core\IAmFrameworkConfig $oConfig
         * @throws DispatchException
         */
        private function checkIfClassExists($sFullEndPointClassName, $oConfig)
        {

            if (!class_exists($sFullEndPointClassName)) {

                if ($oConfig->getEndPointClasses() === null && $oConfig->getEndPointNamespaces() === null) {

                    throw new DispatchException(
                        new Label('Access to EndPoints denied. Please define EndPointClasses or EndPointNamespaces in config.'),
                        Status::INTERNAL_SERVER_ERROR
                    );

                } else {

                    $aAllowedNamespaces = $oConfig->getEndPointNamespaces() !== null ? $oConfig->getEndPointNamespaces() : array();
                    $aAllowedClasses = $oConfig->getEndPointClasses() !== null ? $oConfig->getEndPointClasses() : array();

                    throw new DispatchException(
                        sprintf(
                            new Label('Could not resolve class [%s] as EndPoint. Currently EndPoints are defined as classes within [%s] namespaces or classes [%s]!'),
                            $sFullEndPointClassName,
                            implode(', ', $aAllowedNamespaces),
                            implode(', ', $aAllowedClasses)
                        ),
                        Status::NOT_FOUND
                    );
                }

            }

        }

        /**
         * @param IChangeRequest $oRequest
         * @param \AtomPie\AnnotationTag\Client $oClientAnnotation
         * @return mixed
         */
        private function validateClient(
            IChangeRequest $oRequest,
            Client $oClientAnnotation = null
        ) {

            if ($oClientAnnotation !== null) {
                $oValidator = new ClientAnnotationValidator($oClientAnnotation);
                $oValidator->validate($oRequest);
            }

        }

    }

}