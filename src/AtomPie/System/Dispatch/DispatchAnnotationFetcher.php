<?php
namespace AtomPie\System\Dispatch {

    use AtomPie\Annotation\AnnotationParser;
    use AtomPie\Annotation\AnnotationTag;
    use AtomPie\Core\Annotation\Tag\Authorize;
    use AtomPie\Core\Annotation\Tag\Client;
    use AtomPie\Core\Annotation\Tag\EndPoint;
    use AtomPie\Core\Annotation\Tag\Log;
    use AtomPie\Core\Annotation\Tag\SaveState;
    use AtomPie\Core\Annotation\Tag\Header;

    /**
     * Class AnnotationHandler
     * @package Workshop\FrontEnd\Dispatch
     */
    class DispatchAnnotationFetcher
    {

        private $aDefaultAnnotationMapping = array(
            'EndPoint' => EndPoint::class,
            'SaveState' => SaveState::class,
            'Header' => Header::class,
            'Client' => Client::class,
            'Authorize' => Authorize::class,
            'Log' => Log::class,
        ); // Set default set of Annotations


        /**
         * @param $mObject
         * @param $sMethod
         * @return Client | null
         */
        public function getClientAnnotation($mObject, $sMethod = null)
        {

            $oParser = new AnnotationParser();
            $aAnnotations = $oParser->getAnnotationsFromObjectOrMethod(
                $this->aDefaultAnnotationMapping,
                $mObject,
                $sMethod
            );

            return AnnotationTag::getAnnotationByType($aAnnotations, Client::class);
        }

        /**
         * @param mixed $mObject
         * @return EndPoint | null
         */
        public function getEndPointClassAnnotation($mObject)
        {

            $oParser = new AnnotationParser();
            $aAnnotations = $oParser->getAnnotationsFromObjectOrMethod(
                $this->aDefaultAnnotationMapping,
                $mObject
            );

            return AnnotationTag::getAnnotationByType($aAnnotations, EndPoint::class);
        }

        /**
         * @param mixed $mObject
         * @param string $sMethod
         * @return \AtomPie\Core\Annotation\Tag\EndPoint | null
         */
        public function getEndPointAnnotation($mObject, $sMethod)
        {

            $oParser = new AnnotationParser();
            $aAnnotations = $oParser->getAnnotationsFromObjectOrMethod(
                $this->aDefaultAnnotationMapping,
                $mObject,
                $sMethod
            );

            return AnnotationTag::getAnnotationByType($aAnnotations, EndPoint::class);
        }

        /**
         * @param $mObject
         * @param string|null $sMethod
         * @return \AtomPie\Core\Annotation\Tag\Header[] | null
         */
        public function getHeaderAnnotation($mObject, $sMethod = null)
        {

            $oParser = new AnnotationParser();
            $aAnnotationCollection = $oParser->getAnnotationsFromObjectOrMethod(
                $this->aDefaultAnnotationMapping,
                $mObject,
                $sMethod
            );

            return AnnotationTag::getAnnotationsByType($aAnnotationCollection, Header::class);

        }

    }

}