<?php
namespace AtomPie\AnnotationTag {

    use AtomPie\I18n\Label;
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
                    throw new Exception(sprintf(new Label('Please pass attributes as array of %s.'),
                        IAmNameValuePairImmutable::class));
                }

                $sName = $oAttribute->getName();
                $aAllowedAttributes = $this->getAllowedAttributes();
                if (!is_array($aAllowedAttributes)) {
                    throw new Exception(sprintf(new Label('Please define allowed attributes as array in class %s'),
                        get_class($this)));
                }

                if (in_array($sName, $aAllowedAttributes)) {
                    $this->$sName = $oAttribute->getValue();
                }

            }
        }

        /**
         * @return array
         */
        abstract protected function getAllowedAttributes();

    }

}
