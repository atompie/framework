<?php
namespace AtomPie\Gui\Component {

    use AtomPie\Annotation\AnnotationParser;
    use AtomPie\Web\Boundary\IPersistParamState;
    use AtomPie\DependencyInjection\Boundary\IAmDependencyMetaData;
    use AtomPie\AnnotationTag\SaveState;
    use AtomPie\Web\Boundary\IAmRequest;

    class ComponentDependencyFactory
    {

        public static function factoryComponentRequestParam(
            IAmDependencyMetaData $oMeta,
            IAmRequest $oRequest,
            IPersistParamState $oStatePersister
        ) {

            $sClassType = $oMeta->getCalledClassType();
            $sMethod = $oMeta->getCalledMethod();
            $oParameter = $oMeta->getParamMetaData();
            $oClassOfParameter = $oParameter->getClass();
            $oObject = $oMeta->getObject();

            $oParamFactory = new ComponentParamFactory(
                $oObject,
                $oMeta->getCalledFunctionMetaData()
            );

            // Set default set of Annotations
            $aAnnotationMapping = array(
                'SaveState' => SaveState::class,
            );

            $oParser = new AnnotationParser();
            $oAnnotations = $oParser->getAnnotationsFromObjectOrMethod($aAnnotationMapping, $sClassType, $sMethod);

            return $oParamFactory->factoryComponentParamFromRequest(
                $oRequest,
                $oAnnotations,
                $oClassOfParameter,
                $oParameter,
                $oStatePersister
            );

        }
    }

}
