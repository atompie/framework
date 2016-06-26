<?php
namespace AtomPie\Gui\Component {

    use AtomPie\Annotation\AnnotationParser;
    use AtomPie\Web\Boundary\IPersistParamState;
    use AtomPie\DependencyInjection\Boundary\IAmDependencyMetaData;
    use AtomPie\Gui\Component\Annotation\Tag\SaveState;
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
            $aAnnotations = $oParser->getAnnotationsFromObjectOrMethod($aAnnotationMapping, $sClassType, $sMethod);

            return $oParamFactory->factoryComponentParamFromRequest(
                $oRequest,
                $aAnnotations,
                $oClassOfParameter,
                $oParameter,
                $oStatePersister
            );

        }
    }

}
