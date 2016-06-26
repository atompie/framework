<?php
namespace AtomPie\Annotation {

    use AtomPie\Html\Attribute;
    use AtomPie\Html\Attributes;

    class AnnotationParser
    {

        private static $aAnnotationRepository;

        /**
         * @param $sPhpDoc
         * @return array|null
         */
        public function parse($sPhpDoc)
        {
            if ($sPhpDoc !== false && preg_match_all('/@(?!property|param|return)(.*)[^\n]/i', $sPhpDoc, $aResult)) {
                $aCollection = array();
                foreach ($aResult[0] as $sLine) {
                    // Annotation with attributes
                    if (preg_match_all('/@(?!property|param|return)(.*)\((.*)\)/i', $sLine, $aAnnotations)) {
                        foreach ($aAnnotations[1] as $sKey => $sAnnotation) {
                            $oAnnotation = new AnnotationLine();
                            $oAnnotation->ClassName = $sAnnotation;
                            $oAnnotation->Attributes = new Attributes();
                            // Not empty attributes - add
                            if (!empty($aAnnotations[2][$sKey])) {
                                $sAttributesLine = $aAnnotations[2][$sKey];
                                if (preg_match_all("/([a-z0-9_]+)\\s*=\\s*[\"\\'](.*?)[\"\\']/is", $sAttributesLine,
                                    $aMatches)) {
                                    foreach ($aMatches[0] as $iMatchKey => $sAttributeLine) {
                                        $sName = trim($aMatches[1][$iMatchKey]);
                                        $sValue = trim($aMatches[2][$iMatchKey]);
                                        $oAnnotation->Attributes->addAttribute(new Attribute($sName, $sValue));
                                    }
                                }

                            }
                            $aCollection[trim($sAnnotation)][] = $oAnnotation;
                        }
                    } else {
                        if (preg_match_all('/@(?!property|param|return)(.*)/i', $sLine, $aAnnotations)) {
                            foreach ($aAnnotations[1] as $sAnnotation) {
                                $oAnnotation = new AnnotationLine();
                                $oAnnotation->ClassName = $sAnnotation;
                                $oAnnotation->Attributes = new Attributes();
                                $aCollection[trim($sAnnotation)][] = $oAnnotation;
                            }
                        }
                    }
                }

                return $aCollection;

            }

            return null;
        }

        /**
         * @param \AtomPie\Annotation\AnnotationLine[] $aAnnotations
         * @param array $aAnnotationMapping
         * @return array
         */
        public function getAsTagObjects($aAnnotations, array $aAnnotationMapping)
        {
            $aAnnotationCollection = array();
            foreach ($aAnnotations as $sName => $aAnnotationsLines) {
                foreach ($aAnnotationsLines as $oAnnotation) {
                    if (isset($aAnnotationMapping[$sName])) {
                        $sIndex = $aAnnotationMapping[$sName];
                        $sAnnotationClass = '\\' . $aAnnotationMapping[$sName];
                        $aAnnotationCollection[$sIndex][] = new $sAnnotationClass($oAnnotation->Attributes);
                    }
                }
            }
            return $aAnnotationCollection;
        }

        /**
         * @param $sPhpDoc
         * @param array $aAnnotationClassMapping
         * @return array
         */
        public function getAnnotations($sPhpDoc, array $aAnnotationClassMapping)
        {

            $sKeyString = $sPhpDoc . implode(PHP_EOL, $aAnnotationClassMapping) . PHP_EOL . implode(PHP_EOL,
                    array_keys($aAnnotationClassMapping));
            $sCacheIndex = md5($sKeyString);

            // Proxy (index checks mapping and phpdoc content
            if (!isset(self::$aAnnotationRepository[$sCacheIndex])) {

                $aAnnotations = $this->parse($sPhpDoc);

                $aAnnotationCollection = array();
                if ($aAnnotations !== null) {
                    $aAnnotationCollection = $this->getAsTagObjects(
                        $aAnnotations,
                        $aAnnotationClassMapping
                    );
                }

                self::$aAnnotationRepository[$sCacheIndex] = $aAnnotationCollection;
            }

            return self::$aAnnotationRepository[$sCacheIndex];

        }

        /**
         * @param array $aAllowedAnnotations
         * @param object|string|\ReflectionClass $mObject
         * @param string|null $sMethod
         * @return null|\AtomPie\Annotation\AnnotationTag[]|array
         * @throws Exception
         */
        public function getAnnotationsFromObjectOrMethod(array $aAllowedAnnotations, $mObject, $sMethod = null)
        {

            if ($mObject instanceof \ReflectionFunctionAbstract) {
                $oThisClass = $mObject;
            } elseif ($mObject instanceof \ReflectionClass) {
                $oThisClass = $mObject;
            } else {
                $oThisClass = new \ReflectionClass($mObject);
            }

            if ($sMethod === null) {
                $sPhpDoc = $oThisClass->getDocComment();
            } elseif ($oThisClass instanceof \ReflectionClass) {
                $sPhpDoc = $oThisClass->getMethod($sMethod)->getDocComment();
            } else {
                // Not method or function
                return null;
            }

            // No phpdoc
            if ($sPhpDoc == false) {
                return null;
            }

            return $this->getAnnotations($sPhpDoc, $aAllowedAnnotations);

        }

    }

}
