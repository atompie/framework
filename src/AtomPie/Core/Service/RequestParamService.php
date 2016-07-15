<?php
namespace AtomPie\Core\Service {

    use AtomPie\Annotation\AnnotationTags;
    use AtomPie\AnnotationTag\Authorize;
    use AtomPie\AnnotationTag\Client;
    use AtomPie\AnnotationTag\EndPoint;
    use AtomPie\AnnotationTag\Header;
    use AtomPie\AnnotationTag\Log;
    use Generi\Boundary\IAmKeyValueStore;
    use AtomPie\Annotation\AnnotationParser;
    use AtomPie\DependencyInjection\Boundary\IAmDependencyMetaData;
    use AtomPie\AnnotationTag\SaveState;
    use AtomPie\Web\Boundary\IPersistParamState;
    use AtomPie\Web\Connection\Http\Url\Param;
    use AtomPie\Web\Connection\Http\Url\Param\ParamException;
    use AtomPie\Web\Session\ParamStatePersister;
    use AtomPie\Web\Boundary\IAmEnvironment;
    use AtomPie\Web\Boundary\IAmRequest;
    use AtomPie\Web\Connection\Http\Header\Status;
    use AtomPie\I18n\Label;

    class RequestParamService
    {

        /**
         * @var \ReflectionFunctionAbstract
         */
        private $oMethodOrFunction;

        /**
         * @param \ReflectionFunctionAbstract $oMethodOrFunction
         */
        public function __construct(\ReflectionFunctionAbstract $oMethodOrFunction)
        {
            $this->oMethodOrFunction = $oMethodOrFunction;
        }

        public static function factoryTypeLessRequestParam(
            IAmDependencyMetaData $oMeta,
            IAmEnvironment $oEnvironment,
            $sParamNamespace
        ) {

            $oParamFactory = new self($oMeta->getCalledFunctionMetaData());

            $oStateSaver = new ParamStatePersister($oEnvironment->getSession(), $sParamNamespace);
            return $oParamFactory->factoryTypeLessGlobalParamFromRequest(
                $oEnvironment->getRequest(),
                $oMeta,
                $oStateSaver
            );

        }

        public function factoryTypeLessGlobalParamFromRequest(
            IAmRequest $oRequest,
            IAmDependencyMetaData $oMeta,
            IPersistParamState $oStatePersister
        ) {

            $sParamName = $oMeta->getParamMetaData()->name;
            $oParameter = $oMeta->getParamMetaData();

            if ($oMeta->isClass()) {
                $sClassType = $oMeta->getCalledClassType();
                $sMethod = $oMeta->getCalledMethod();
            } else {
                $sClassType = $oMeta->getCalledFunctionMetaData();
                $sMethod = null;
            }

            $bHasDefaultValue = $oParameter->isDefaultValueAvailable();

            $mParamValue = $oRequest->getParamWithFallbackToBody($sParamName);

            if ($mParamValue === null) {
                // Param not in request but default value is set

                if ($bHasDefaultValue) {
                    $mParamValue = $oParameter->getDefaultValue();
                } else {
                    throw new ParamException(
                        sprintf(new Label('Missing required parameter [%s].'), $sParamName),
                        Status::BAD_REQUEST
                    );
                }
            }

            // Set default set of Annotations
            $aDefaultAnnotationMapping = array(
                'EndPoint' => EndPoint::class,
                'SaveState' => SaveState::class,
                'Header' => Header::class,
                'Client' => Client::class,
                'Authorize' => Authorize::class,
                'Log' => Log::class,
            );

            $oParser = new AnnotationParser();
            $oAnnotations = $oParser->getAnnotationsFromObjectOrMethod(
                $aDefaultAnnotationMapping,
                $sClassType,
                $sMethod
            );

            $oSaveState = $oAnnotations->getFirstAnnotationByType(SaveState::class);

            if ($oSaveState !== null) {

                $bHasParamStateSaved = $oStatePersister->hasState($sParamName);

                // Check if parameter changed or no state is saved but default value is set (save default value)
                if ($oRequest->hasParam($sParamName) || ($bHasDefaultValue && !$bHasParamStateSaved)) {

                    // Has param in request then it must be saved
                    if ($oSaveState instanceof SaveState && $oSaveState->isPersistentParam($sParamName)) {
                        $oParam = new Param($sParamName, $mParamValue);
                        $oStatePersister->saveState($oParam, $oSaveState->As);
                    }

                } else {
                    if ($oSaveState instanceof SaveState && $oSaveState->isPersistentParam($sParamName)) {

                        // Has not param in request then load from persistent storage
                        return $oStatePersister->loadState($sParamName);

                    }
                }

            }

            return $mParamValue;
        }

        /**
         * @param IAmDependencyMetaData $oMeta
         * @param IAmKeyValueStore $oSession
         * @param IAmRequest $oRequest
         * @param $sParamNamespace
         * @return Param
         * @throws ParamException
         */
        public static function factoryRequestParameter(
            IAmDependencyMetaData $oMeta,
            IAmKeyValueStore $oSession,
            IAmRequest $oRequest,
            $sParamNamespace
        ) {

            $oParamFactory = new self($oMeta->getCalledFunctionMetaData());

            // Set default set of Annotations
            $aDefaultAnnotationMapping = array(
                'EndPoint' => EndPoint::class,
                'SaveState' => SaveState::class,
                'Header' => Header::class,
                'Client' => Client::class,
                'Authorize' => Authorize::class,
                'Log' => Log::class,
            );

            $sClassType = $oMeta->getCalledClassType();
            $sMethod = $oMeta->getCalledMethod();
            $oParameter = $oMeta->getParamMetaData();
            $sParamType = $oParameter->getClass()->getName();

            $oParser = new AnnotationParser();
            $oAnnotations = $oParser->getAnnotationsFromObjectOrMethod(
                $aDefaultAnnotationMapping,
                $sClassType,
                $sMethod
            );

            $oStatePersister = new ParamStatePersister($oSession, $sParamNamespace);

            $mParamValue = $oRequest->getParamWithFallbackToBody($oParameter->name);

            return $oParamFactory->factoryGlobalParam(
                $mParamValue,
                $oAnnotations,
                $sParamType,
                $oParameter,
                $oStatePersister
            );
        }

        /**
         * @param $mParamValue
         * @param $oAnnotations
         * @param $sParamType
         * @param \ReflectionParameter $oEndPointParameter
         * @param \AtomPie\Web\Boundary\IPersistParamState $oStatePersister
         * @return Param
         * @throws ParamException
         */
        public function factoryGlobalParam(
            $mParamValue,
            AnnotationTags $oAnnotations,
            $sParamType,
            \ReflectionParameter $oEndPointParameter,
            IPersistParamState $oStatePersister
        ) {

            // Param in request
            $oSaveState = $oAnnotations->getFirstAnnotationByType(SaveState::class);

            if ($mParamValue !== null) {

                // I have got param value from request
                /** @var Param $oParam */
                $oParam = new $sParamType($oEndPointParameter->name, $mParamValue);
                if ($oSaveState !== null && $oSaveState instanceof SaveState && $oSaveState->isPersistentParam($oEndPointParameter->name)) {
                    $oStatePersister->saveState($oParam, $oSaveState->As);
                }

                return $oParam;

            }

            // Param not in request try to load from session

            if ($oSaveState !== null && $oSaveState instanceof SaveState && $oSaveState->isPersistentParam($oEndPointParameter->name)) {

                // Load from persistent storage
                $sValue = $oStatePersister->loadState($oEndPointParameter->name);
                if ($sValue !== null) {
                    return new $sParamType($oEndPointParameter->name, $sValue);
                }

            }

            // Param not in request but default value is set

            if ($oEndPointParameter->isDefaultValueAvailable()) {
                return new $sParamType($oEndPointParameter->name, $oEndPointParameter->getDefaultValue());
            }

            // Error

            throw new ParamException(
                sprintf(new Label('Missing required parameter [%s]. Please provide it in request. Check definition of method [%s::%s()]'),
                    $oEndPointParameter->name,
                    $oEndPointParameter->getDeclaringClass()->getName(),
                    $this->oMethodOrFunction->name
                ),
                Status::BAD_REQUEST
            );

        }

        public static function factoryClosureParameter(
            IAmDependencyMetaData $oMeta,
            IAmKeyValueStore $oSession,
            IAmRequest $oRequest,
            $sParamNamespace
        ) {

            $oParamFactory = new self($oMeta->getCalledFunctionMetaData());

            // Set default set of Annotations
            $aDefaultAnnotationMapping = array(
                'EndPoint' => EndPoint::class,
                'SaveState' => SaveState::class,
                'Header' => Header::class,
                'Client' => Client::class,
                'Authorize' => Authorize::class,
                'Log' => Log::class,
            );

            $oParser = new AnnotationParser();
            $oAnnotations = $oParser->getAnnotationsFromObjectOrMethod(
                $aDefaultAnnotationMapping,
                $oMeta->getCalledFunctionMetaData()
            );
            $oStatePersister = new ParamStatePersister($oSession, $sParamNamespace);

            $oParameter = $oMeta->getParamMetaData();
            $mParamValue = $oRequest->getParamWithFallbackToBody($oParameter->name);
            $sParamType = $oParameter->getClass()->name;

            return $oParamFactory->factoryGlobalParam(
                $mParamValue,
                $oAnnotations,
                $sParamType,
                $oParameter,
                $oStatePersister
            );
        }

    }

}
