<?php
namespace AtomPie\Annotation {

    class AnnotationParser
    {

        private static $aAnnotationRepository;

        /**
         * @param $sPhpDoc
         * @return array |null
         */
        public function parse($sPhpDoc)
        {
            if ($sPhpDoc !== false && preg_match_all('/@(?!property|param|return)(.*)[^\n]/i', $sPhpDoc, $aResult)) {
                $aCollection = array();
                foreach ($aResult[0] as $sLine) {
                    // Annotation with attributes
                    if (preg_match_all('/@(?!property|param|return)(.*)\((.*)\)/i', $sLine, $aAnnotations)) {
                        foreach ($aAnnotations[1] as $sKey => $sAnnotation) {
                            $oAnnotationLine = new AnnotationLine($sAnnotation);
                            // Not empty attributes - add
                            if (!empty($aAnnotations[2][$sKey])) {
                                $sAttributesLine = $aAnnotations[2][$sKey];
                                if (preg_match_all("/([a-z0-9_]+)\\s*=\\s*[\"\\'](.*?)[\"\\']/is", $sAttributesLine,
                                    $aMatches)) {
                                    foreach ($aMatches[0] as $iMatchKey => $sAttributeLine) {
                                        $sName = trim($aMatches[1][$iMatchKey]);
                                        $sValue = trim($aMatches[2][$iMatchKey]);
                                        $oAnnotationLine->Attributes->addAttribute(new Attribute($sName, $sValue));
                                    }
                                }

                            }
                            $aCollection[trim($sAnnotation)][] = $oAnnotationLine;
                        }
                    } else {
                        if (preg_match_all('/@(?!property|param|return)(.*)/i', $sLine, $aAnnotations)) {
                            foreach ($aAnnotations[1] as $sAnnotation) {
                                $oAnnotationLine = new AnnotationLine($sAnnotation);
                                $aCollection[trim($sAnnotation)][] = $oAnnotationLine;
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
                /**
                 * @var $oAnnotationLine AnnotationLine
                 */
                foreach ($aAnnotationsLines as $oAnnotationLine) {
                    if (isset($aAnnotationMapping[$sName])) {
                        $sIndex = $aAnnotationMapping[$sName];
                        $sAnnotationClass = '\\' . $aAnnotationMapping[$sName];
                        $aAnnotationCollection[$sIndex][] = new $sAnnotationClass($oAnnotationLine->Attributes);
                    }
                }
            }
            return $aAnnotationCollection;
        }

        /**
         * @param $sPhpDoc
         * @param array $aAnnotationClassMapping
         * @return AnnotationTags
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

            return new AnnotationTags(self::$aAnnotationRepository[$sCacheIndex]);

        }

        /**
         * @param array $aAllowedAnnotations
         * @param object|string|\ReflectionClass $mObject
         * @param string|null $sMethod
         * @return AnnotationTags
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
                return new AnnotationTags([]);
            }

            // No phpdoc
            if ($sPhpDoc == false) {
                return new AnnotationTags([]);
            }

            return $this->getAnnotations($sPhpDoc, $aAllowedAnnotations);

        }

    }

}
