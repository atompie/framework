<?php
namespace AtomPie\System\Dispatch {

    use AtomPie\Annotation\AnnotationParser;
    use AtomPie\AnnotationTag\Authorize;
    use AtomPie\AnnotationTag\Client;
    use AtomPie\AnnotationTag\EndPoint;
    use AtomPie\AnnotationTag\Log;
    use AtomPie\AnnotationTag\SaveState;
    use AtomPie\AnnotationTag\Header;

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
         * @return \AtomPie\AnnotationTag\Client | null
         */
        public function getClientAnnotation($mObject, $sMethod = null)
        {

            $oParser = new AnnotationParser();
            $oAnnotations = $oParser->getAnnotationsFromObjectOrMethod(
                $this->aDefaultAnnotationMapping,
                $mObject,
                $sMethod
            );

            return $oAnnotations->getFirstAnnotationByType(Client::class);
        }

        /**
         * @param mixed $mObject
         * @return \AtomPie\AnnotationTag\EndPoint | null
         */
        public function getEndPointClassAnnotation($mObject)
        {

            $oParser = new AnnotationParser();
            $oAnnotations = $oParser->getAnnotationsFromObjectOrMethod(
                $this->aDefaultAnnotationMapping,
                $mObject
            );

            return $oAnnotations->getFirstAnnotationByType(EndPoint::class);
        }

        /**
         * @param mixed $mObject
         * @param string $sMethod
         * @return \AtomPie\AnnotationTag\EndPoint | null
         */
        public function getEndPointAnnotation($mObject, $sMethod)
        {

            $oParser = new AnnotationParser();
            $oAnnotations = $oParser->getAnnotationsFromObjectOrMethod(
                $this->aDefaultAnnotationMapping,
                $mObject,
                $sMethod
            );

            return $oAnnotations->getFirstAnnotationByType(EndPoint::class);
        }

        /**
         * @param $mObject
         * @param string|null $sMethod
         * @return \AtomPie\AnnotationTag\Header[] | null
         */
        public function getHeaderAnnotation($mObject, $sMethod = null)
        {
            $oParser = new AnnotationParser();
            $oAnnotations = $oParser->getAnnotationsFromObjectOrMethod(
                $this->aDefaultAnnotationMapping,
                $mObject,
                $sMethod
            );

            return $oAnnotations->getAnnotationsByType(Header::class);

        }

    }

}