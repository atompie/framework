<?php
namespace AtomPie\Gui\Component {

    use AtomPie\Annotation\AnnotationTags;
    use Generi\Boundary\ICanBeIdentified;
    use Generi\Object;
    use AtomPie\Boundary\Gui\Component\IAmComponentParam;
    use AtomPie\Web\Boundary\IPersistParamState;
    use AtomPie\AnnotationTag\SaveState;
    use AtomPie\Web\Boundary\IAmRequest;
    use AtomPie\Web\Connection\Http\Header\Status;
    use AtomPie\Web\Connection\Http\Url\Param;
    use AtomPie\I18n\Label;

    class ComponentParamFactory
    {
        /**
         * @var \ReflectionFunctionAbstract
         */
        private $oMethodOrFunction;

        /**
         * @var object | string
         */
        private $mObjectOrClassName;


        /**
         * @param object | string $mObjectOrClassName Any component
         * @param \ReflectionFunctionAbstract $oMethodOrFunction
         */
        public function __construct($mObjectOrClassName, \ReflectionFunctionAbstract $oMethodOrFunction)
        {
            $this->oMethodOrFunction = $oMethodOrFunction;
            $this->mObjectOrClassName = $mObjectOrClassName;
        }

        /**
         * @param IAmRequest $oRequest
         * @param $oAnnotations
         * @param \ReflectionClass $oClassOfParameter
         * @param \ReflectionParameter $oParameter
         * @param IPersistParamState $oStatePersister
         * @return Param
         * @throws Exception
         * @throws Param\ParamException
         */
        public function factoryComponentParamFromRequest(
            IAmRequest $oRequest,
            AnnotationTags $oAnnotations,
            \ReflectionClass $oClassOfParameter,
            \ReflectionParameter $oParameter,
            IPersistParamState $oStatePersister
        ) {

            $sVariableName = $oParameter->name;

            if (!is_object($this->mObjectOrClassName)) {
                throw new Exception(
                    sprintf(new Label('ComponentParam [%s] can not be constructed within static method. Check method [%s::%s()]'),
                        $sVariableName,
                        $oParameter->getDeclaringClass()->name,
                        $this->oMethodOrFunction->name
                    )
                );
            }

            $sParamType = $oClassOfParameter->name;
            /** @var SaveState $oSaveState */
            $oSaveState = $oAnnotations->getFirstAnnotationByType(SaveState::class);
            $aValueFromRequest = $this->getRequestComponentValue($oRequest, $sVariableName);

            // Value is OK

            if (false !== $aValueFromRequest) {
                // I have got param value from request

                list($sValue, $sComponentContext) = $aValueFromRequest;

                /** @var \AtomPie\Boundary\Gui\Component\IAmComponentParam $oParam */
                $oParam = new $sParamType($sVariableName, $sValue);
                $oParam->setComponentContext($sComponentContext);

                if ($this->isPersistentParam($oSaveState, $sVariableName)) {
                    $oStatePersister->saveState($oParam, $oSaveState->As, $sComponentContext);
                }

                return $oParam;
            }
            // Check persistent param

            if ($this->isPersistentParam($oSaveState, $sVariableName)) {

                // Only invocations from type Part may have local params
                if ($this->mObjectOrClassName instanceof ICanBeIdentified) {

                    $oComponent = $this->mObjectOrClassName;

                    $oParamContext = new ComponentParamSessionKey($oComponent);

                    // Load from persistent storage
                    $sValue = $oStatePersister->loadState($sVariableName, $oParamContext->__toString());
                    if (null !== $sValue) {

                        /** @var IAmComponentParam $oParam */
                        $oParam = new $sParamType($sVariableName, $sValue);
                        $oParam->setComponentContext($oParamContext->__toString());

                        return $oParam;

                    }
                }

            }

            // Empty but optional

            if ($oParameter->isDefaultValueAvailable()) {
                return new $sParamType($sVariableName, $oParameter->getDefaultValue());
            }

            // Error

            $sParamName = isset($sComponentParamContext)
                ? $sVariableName . '[' . $sComponentParamContext . ']'
                : isset($sComponentType)
                    ? $sVariableName . '[' . $sComponentType . ']'
                    : $sVariableName;

            throw new Param\ParamException(
                sprintf(
                    new Label('Missing required parameter [%s]. Please provide it in request. Check definition of method [%s::%s()]'),
                    $sParamName,
                    $oParameter->getDeclaringClass()->name,
                    $this->oMethodOrFunction->name
                ),
                Status::BAD_REQUEST
            );

        }

        /**
         * @param SaveState $oSaveState
         * @param $sVariableName
         * @return bool
         */
        private function isPersistentParam($oSaveState, $sVariableName)
        {
            return $oSaveState != null && $oSaveState->isPersistentParam($sVariableName);
        }

        /**
         * @param IAmRequest $oRequest
         * @param $sParamName
         * @return array|bool
         */
        private function getRequestComponentValue($oRequest, $sParamName)
        {

            if ($this->mObjectOrClassName instanceof Object) {

                $sComponentType = $this->mObjectOrClassName->getType()->getName();

                // Get param
                $mRequestParam = $oRequest->getParamWithFallbackToBody($sParamName);

                // Has any params

                if (isset($mRequestParam) && is_array($mRequestParam)) {

                    $sParamKey = $this->getComponentKey($sComponentType);

                    if (isset($mRequestParam[$sParamKey])) {
                        // ParamName[ComponentType.Name] or ParamName[ComponentType]
                        return array(
                            $mRequestParam[$sParamKey],
                            $sParamKey  // Component Context
                        );

                    } else {
                        if (isset($mRequestParam[$sComponentType])) {
                            // Fall back to component type
                            // ParamName[ComponentType]
                            return array(
                                $mRequestParam[$sComponentType],
                                $sComponentType  // Component Context
                            );
                        }
                    }
                }

            }

            return false;

        }

        /**
         * @param $sComponentType
         * @return string
         */
        private function getComponentKey($sComponentType)
        {
            if ($this->mObjectOrClassName instanceof ICanBeIdentified) {

                $oContext = new ComponentParamSessionKey($this->mObjectOrClassName);
                // ParamName[ComponentType.Name]
                $sParamKey = $oContext->__toString();
                return $sParamKey;

            } else {

                // ParamName[ComponentType]
                $sParamKey = $sComponentType;
                return $sParamKey;

            }
        }

    }

}
