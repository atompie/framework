<?php
namespace AtomPie\Annotation {

    use Generi\Boundary\IAmNameValuePairImmutable;

    abstract class AnnotationTag
    {
        /**
         * @param \Iterator $aAttributes
         * @throws Exception
         */
        public function __construct(\Iterator $aAttributes)
        {
            foreach ($aAttributes as $oAttribute) {

                if(!$oAttribute instanceof IAmNameValuePairImmutable) {
                    throw new Exception(sprintf('Please pass attributes as array in class %s constructor',
                        get_class($this)));
                }

                $sName = $oAttribute->getName();
                $aAllowedAttributes = $this->getAllowedAttributes();
                if (!is_array($aAllowedAttributes)) {
                    throw new Exception(sprintf('Please define allowed attributes as array in class %s',
                        get_class($this)));
                }

                if (in_array($sName, $aAllowedAttributes)) {
                    $this->setAttribute($sName, $oAttribute->getValue());
                }

            }
        }

        /**
         * @return array
         */
        abstract protected function getAllowedAttributes();

        private function setAttribute($sName, $sValue)
        {
            $this->$sName = $sValue;
        }

        /**
         * @param $aAnnotations
         * @param $sType
         * @return null | AnnotationTag
         */
        public static function getAnnotationByType($aAnnotations, $sType)
        {

            if (!empty($aAnnotations) && isset($aAnnotations[$sType])) {
                reset($aAnnotations[$sType]);
                return current($aAnnotations[$sType]);
            }

            return null;

        }

        /**
         * @param $aAnnotations
         * @param $sType
         * @return null | AnnotationTag[]
         */
        public static function getAnnotationsByType($aAnnotations, $sType)
        {

            if (!empty($aAnnotations) && isset($aAnnotations[$sType])) {
                return $aAnnotations[$sType];
            }

            return null;

        }

    }

}
