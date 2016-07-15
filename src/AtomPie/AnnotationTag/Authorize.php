<?php
namespace AtomPie\AnnotationTag {

    use AtomPie\I18n\Label;

    /**
     * @package Workshop\MetaData\Annotation
     * @Annotation
     * @Target("CLASS","METHOD")
     */
    final class Authorize extends AnnotationTag
    {

        /**
         * @var string
         */
        public $AuthToken;

        /**
         * @Enum({"Static","Basic","Method"})
         */
        public $AuthType;

        /**
         * @var string
         */
        public $ResourceIndex;

        /**
         * @var string
         */
        public $ErrorMessage;

        public function __construct(\Iterator $aAttributes)
        {
            parent::__construct($aAttributes);

            if (!isset($this->AuthType)) {
                throw new Exception(
                    new Label('AuthType attribute does note exist in @Authorize annotation. Please provide AuthType in order to set authorization type.')
                );
            }

            if (strtolower($this->AuthType) == 'basic' && !isset($this->AuthToken)) {
                throw new Exception(
                    new Label('AuthToken attribute does note exist in @Authorize annotation. Please provide AuthToken for basic authorization in form of user:password.')
                );
            }

            if (strtolower($this->AuthType) == 'static' && !isset($this->AuthToken)) {
                throw new Exception(
                    new Label('AuthToken attribute does note exist in @Authorize annotation. Please provide AuthToken for static authorization in form of public/private.')
                );
            }

            if (strtolower($this->AuthType) == 'method' && !isset($this->AuthToken)) {
                throw new Exception(
                    new Label('AuthToken attribute does note exist in @Authorize annotation. Please provide AuthToken in form of class.method().')
                );
            }

        }

        /**
         * @return array
         */
        protected function getAllowedAttributes()
        {
            return array('AuthToken', 'AuthType', 'ResourceIndex', 'ErrorMessage');
        }

    }

}
