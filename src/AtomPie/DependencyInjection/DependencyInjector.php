<?php
namespace AtomPie\DependencyInjection {

    use AtomPie\DependencyInjection\Boundary\IAmDependencyMetaData;
    use AtomPie\DependencyInjection\Boundary\ICanInvokeDependentClasses;
    use AtomPie\DependencyInjection\Boundary\IConstructInjection;
    use AtomPie\I18n\Label;
    use AtomPie\System\ContractFillers;

    class DependencyInjector implements ICanInvokeDependentClasses
    {

        private $oObject;
        
        const DEPENDENCY_CONTAINER_CLOSURE = 'DependencyContainerClosure';
        
        /**
         * @var IConstructInjection
         */
        private $oContainer;

        /**
         * @var \ReflectionClass
         */
        private $oReflectionClass;
        /**
         * @var ContractFillers
         */
        private $oContactFillers;

        /**
         * DependencyInjector constructor.
         * @param IConstructInjection $oContainer
         * @param ContractFillers $oContactFillers
         */
        public function __construct(IConstructInjection $oContainer, ContractFillers $oContactFillers = null)
        {
            $this->oContainer = $oContainer;
            $this->oContactFillers = $oContactFillers;
        }

        public function invokeClosure($sClosureId, \Closure $pClosure, array $aCustomDependency = null)
        {

            if (!empty($aCustomDependency)) {

                if ($this->oContainer instanceof DependencyContainer) {
                    $oDependency = (new Dependency())
                        ->addDependency($aCustomDependency);

                    // DO NOT mutate container
                    $oLocalContainer = clone $this->oContainer;

                    $oLocalContainer->addDependency(Dependency::CLOSURE, $sClosureId, $oDependency);

                    return $this->__invokeClosure($sClosureId, $pClosure, $oLocalContainer);
                }

            }

            return $this->__invokeClosure($sClosureId, $pClosure, $this->oContainer);
        }

        /**
         * @param $oObject
         * @param $sMethod
         * @param array|null $aCustomDependency
         * @return mixed
         * @throws Exception
         */
        public function invokeMethod($oObject, $sMethod, array $aCustomDependency = null)
        {
            $this->oObject = $oObject;
            $this->oReflectionClass = new \ReflectionClass($oObject);
            if (!$this->oReflectionClass->hasMethod($sMethod)) {
                throw new Exception(sprintf('Method [%s] does not exist', $sMethod));
            }

            if (!empty($aCustomDependency)) {

                if ($this->oContainer instanceof DependencyContainer) {
                    $sClassName = get_class($oObject);
                    $oCustomDependency =
                        (new Dependency($sClassName, $sMethod))
                            ->addDependency($aCustomDependency);

                    // DO NOT mutate container
                    $oLocalContainer = clone $this->oContainer;

                    $oLocalContainer->addDependency($sClassName, $sMethod, $oCustomDependency);

                    return $this->__invokeMethod($this->oReflectionClass, $sMethod, $oLocalContainer);

                }

            }

            return $this->__invokeMethod($this->oReflectionClass, $sMethod, $this->oContainer);

        }

        /**
         * @param $sClassTypeOfFunctionId
         * @param $sMethod
         * @param \ReflectionParameter $oParameter
         * @param \ReflectionFunctionAbstract $oMethodOrFunction
         * @param IConstructInjection $oLocalContainer
         * @return mixed
         * @throws Exception
         */
        private function injectParam(
            $sClassTypeOfFunctionId,
            $sMethod,
            \ReflectionParameter $oParameter,
            \ReflectionFunctionAbstract $oMethodOrFunction,
            IConstructInjection $oLocalContainer
        ) {

            // Type less param
            if (!$this->hasParamDefinedType($oParameter)) {

                return $this->invokeTypeLessDependencyFactory(
                    $sClassTypeOfFunctionId, 
                    $sMethod, 
                    $oParameter,
                    $oMethodOrFunction, 
                    $oLocalContainer);

            }

            // Param with type

            return $this->invokeTypeFulDependencyFactory(
                $oLocalContainer,
                $sClassTypeOfFunctionId,
                $sMethod,
                $oMethodOrFunction,
                $oParameter
            );

        }

        /**
         * Builds injected parameter using Dependency container or
         * if not defined static factory method __build.
         *
         * @param IConstructInjection $oContainer
         * @param $sClassType
         * @param $sMethod
         * @param \ReflectionFunctionAbstract $oMethodOrFunction
         * @param \ReflectionParameter $oInjectionParameter
         * @return mixed
         * @throws Exception
         */
        private function invokeTypeFulDependencyFactory(
            IConstructInjection $oContainer,
            $sClassType,
            $sMethod,
            \ReflectionFunctionAbstract $oMethodOrFunction,
            \ReflectionParameter $oInjectionParameter
        ) {

            $sInjectionTypeName = $oInjectionParameter->getClass()->name;

            $oDependency = $oContainer->getInjectionClosureFor($sClassType, $sMethod);
            if (false !== $oDependency) {

                // Dependency injection

                /** @var Dependency $oDependency */
                $aDependencies = $oDependency->getDependencies();

                if (!empty($aDependencies)) {

                    $oInjectionReflectionClass = new \ReflectionClass($sInjectionTypeName);
                    foreach ($aDependencies as $sInjectionTypeHint => $pDependencyClosure) {
                        if ($sInjectionTypeHint == Dependency::TYPE_LESS) {
                            continue;
                        }

                        $sContractFillerClassName = $this->replaceWithContractFillerIfExists(
                            $sInjectionTypeHint
                            , $sClassType
                            , $sMethod);

                        // Is correct type for injection?
                        if(
                            $oInjectionReflectionClass->name == $sContractFillerClassName 
                            || $oInjectionReflectionClass->isSubclassOf($sContractFillerClassName)
                        ) {
                        
                            $oObject = $this->oObject;

                            $oInjector = new DependencyInjector($oContainer, $this->oContactFillers);

                            return $oInjector->invokeClosure(
                                self::DEPENDENCY_CONTAINER_CLOSURE,
                                $pDependencyClosure,
                                [
                                    IAmDependencyMetaData::class => function() use(
                                        $oObject,
                                        $sClassType,
                                        $sMethod,
                                        $oMethodOrFunction,
                                        $oInjectionParameter
                                    )
                                    {
                                        return new DependencyInvokeMetaData(
                                            $oObject,
                                            $sClassType,
                                            $sMethod,
                                            $oMethodOrFunction,
                                            $oInjectionParameter
                                        );
                                    }
                                ]
                            );
                        }
                    }
                }

            } 
            
            // Factory method

            $sContractClassName = $this->replaceWithContractFillerIfExists(
                $sInjectionTypeName
                , $sClassType
                , $sMethod);
            
            if(!method_exists($sContractClassName, '__build')) {
                throw new Exception(sprintf(
                        new Label('Error while trying to invoke [%s:%s] dependency injection. Please check if factory method __build() exists.'),
                        $sInjectionTypeName,
                        $sClassType,
                        $sMethod
                    )
                );
            }
            
            // Constrain build throws exception
            $this->constrainBuild($sClassType, $sInjectionTypeName);

            $oInjector = new DependencyInjector($oContainer, $this->oContactFillers);
            return $oInjector->invokeMethod($sContractClassName, '__build');

        }

        /**
         * @param \ReflectionParameter $oParameter
         * @return bool
         */
        private function hasParamDefinedType($oParameter)
        {
            return null !== $oParameter->getClass();
            // || null === $oParameter->getDeclaringClass(); // Means this is function not method
        }

        /**
         * @param $sClassTypeOrFunctionId
         * @param $sMethod
         * @param \ReflectionFunctionAbstract $oFunctionAbstract
         * @param IConstructInjection $oContainer
         * @return array
         * @throws Exception
         */
        private function getParameters(
            $sClassTypeOrFunctionId,
            $sMethod,
            \ReflectionFunctionAbstract $oFunctionAbstract,
            IConstructInjection $oContainer
        ) {

            $aParamsFromMethod = $oFunctionAbstract->getParameters();
            $aCallParameters = [];
            
            // Must inject params - there are not empty
            if (!empty($aParamsFromMethod)) {
                // Get parameters
                foreach ($aParamsFromMethod as $oParameter) {
                    /* @var $oParameter \ReflectionParameter */
                    $iPosition = $oParameter->getPosition();
                    $aCallParameters[$iPosition] = $this->injectParam(
                        $sClassTypeOrFunctionId,
                        $sMethod,
                        $oParameter,
                        $oFunctionAbstract,
                        $oContainer);
                }
                return $aCallParameters;
            }
            
            return $aCallParameters;
        }

        /**
         * @param $sClosureId
         * @param \Closure $pClosure
         * @param IConstructInjection $oContainer
         * @return mixed
         */
        private function __invokeClosure($sClosureId, \Closure $pClosure, $oContainer)
        {

            $oFunction = new \ReflectionFunction($pClosure);
            $aCallParameters = $this->getParameters(
                Dependency::CLOSURE,
                $sClosureId,
                $oFunction,
                $oContainer
            );

            return $oFunction->invokeArgs(
                $aCallParameters
            );
        }

        /**
         * @param \ReflectionClass $oReflectionClass
         * @param string $sMethod
         * @param IConstructInjection $oContainer
         * @return mixed
         */
        private function __invokeMethod($oReflectionClass, $sMethod, IConstructInjection $oContainer)
        {
            $oMethod = $oReflectionClass->getMethod($sMethod);

            $aCallParameters = $this->getParameters(
                $oReflectionClass->name,
                $sMethod,
                $oMethod,
                $oContainer
            );

            return $oMethod->invokeArgs(
                is_object($this->oObject)
                    ? $this->oObject
                    : null,
                $aCallParameters
            );
        }

        /**
         * @param $sClassTypeOfFunctionId
         * @param $sMethod
         * @param \ReflectionParameter $oParameter
         * @param \ReflectionFunctionAbstract $oMethodOrFunction
         * @param IConstructInjection $oLocalContainer
         * @return mixed
         * @throws Exception
         */
        private function invokeTypeLessDependencyFactory(
            $sClassTypeOfFunctionId,
            $sMethod,
            \ReflectionParameter $oParameter,
            \ReflectionFunctionAbstract $oMethodOrFunction,
            IConstructInjection $oLocalContainer
        ) {
            $oDependencyDefinition = $oLocalContainer->getInjectionClosureFor($sClassTypeOfFunctionId, $sMethod);

            if ($oDependencyDefinition == false || !$oDependencyDefinition->hasTypeLessDependency()) {
                throw new Exception(
                    sprintf('Dependency injection failed. Method [%s:%s] has not defined type in param [%s].',
                        $oParameter->getDeclaringClass()->getName(),
                        $oMethodOrFunction->getName(),
                        $oParameter->getName()
                    )
                );
            }

            $pDependencyClosure = $oDependencyDefinition->getTypeLessDependency();

            return $pDependencyClosure(
                new DependencyInvokeMetaData(
                    $this->oObject,
                    $sClassTypeOfFunctionId,
                    $sMethod,
                    $oMethodOrFunction,
                    $oParameter
                )
            );
        }

        /**
         * @param $sClassType
         * @param $sInjectionTypeName
         * @throws Exception
         */
        private function constrainBuild($sClassType, $sInjectionTypeName)
        {
            if (method_exists($sInjectionTypeName, '__constrainBuild')) {
                $aConstrainMapping = forward_static_call([$sInjectionTypeName, '__constrainBuild']);
                
                if(!is_array($aConstrainMapping)) {
                    throw new Exception(
                        new Label('Method __constrainBuild must return array of allowed classes that can use this factory method')
                    );
                }
                
                if (!in_array($sClassType, $aConstrainMapping)) {
                    throw new Exception(
                        sprintf(
                            new Label('__build method is not allowed to be used outside following classes %s'),
                            implode(',', $aConstrainMapping)
                        )
                    );
                }
            }
        }

        /**
         * @param $sInjectionTypeName
         * @param $sClassType
         * @param $sMethod
         * @return string
         */
        private function replaceWithContractFillerIfExists($sInjectionTypeName, $sClassType, $sMethod)
        {
            // No contract fillers
            if($this->oContactFillers == null) {
                return $sInjectionTypeName;
            }
            
            // There are contact fillers
            $sContractFillerClass = $this->oContactFillers->getContractFillerFor(
                $sInjectionTypeName
                , $sClassType
                , $sMethod
            );

            return ($sContractFillerClass === null)
                ? $sInjectionTypeName
                : $sContractFillerClass;
            
        }

    }

}
