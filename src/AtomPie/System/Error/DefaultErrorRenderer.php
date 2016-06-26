<?php
namespace AtomPie\System\Error {

    use Generi\Boundary\IStringable;
    use AtomPie\System\Exception;
    use AtomPie\I18n\Label;
    use AtomPie\System\IO\File;
    use AtomPie\Web\Boundary\IRecognizeMediaType;

    class DefaultErrorRenderer implements IStringable
    {

        public $ErrorMessage;
        public $StackTrace;
        public $Line;
        public $File;
        public $Code;
        public $ExceptionType;

        /**
         * @var \Exception
         */
        public $InternalException;

        /**
         * @var IRecognizeMediaType
         */
        private $oContentType;

        public function __construct(IRecognizeMediaType $oContentType)
        {
            $this->oContentType = $oContentType;
        }

        public function __toString()
        {

            if ($this->oContentType->isHtml()) {
                return $this->toHtml();
            } else {
                if ($this->oContentType->isJson()) {
                    return $this->toJson();
                } else {
                    if ($this->oContentType->isXml()) {
                        return $this->toXml();
                    } else {
                        if ($this->oContentType->isText()) {
                            return $this->toText();
                        } else {
                            if ($this->oContentType->isJavascript()) {
                                return $this->toText();
                            }
                        }
                    }
                }
            }

            return $this->ErrorMessage;
        }

        /**
         * Returns rendered view.
         *
         * @return string
         */
        private function toHtml()
        {
            $oTemplateFile = new File(__DIR__ . '/Exception.html.mustache');
            $oMustache = new \Mustache_Engine();
            $sTemplateContent = $oTemplateFile->loadRaw();
            return $oMustache->render(
                $sTemplateContent,
                [
                    'ErrorMessage' => $this->ErrorMessage,
                    'Line' => $this->Line,
                    'StackTrace' => $this->StackTrace,
                    'File' => $this->File,
                    'Code' => $this->Code,
                    'InternalException' => $this->InternalException,
                    'ExceptionType' => $this->ExceptionType,
                    'Thrown' => new Label('thrown.')
                ]
            );
        }

        public function toJson()
        {
            return json_encode(
                $this->getProperties()
            );
        }

        public function toXml()
        {
            try {
                $xml = new \SimpleXMLElement(/** @lang XML */
                    '<Exception/>');
                $this->__toXml($xml, $this->getProperties());
                $mXml = $xml->asXML();
                if ($mXml !== false) {
                    return $mXml;
                }

                throw new Exception(new Label('Could not serialize to XML.'));
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        }

        private function __toXml(\SimpleXMLElement $oRoot, array $aData)
        {
            foreach ($aData as $sKey => $sValue) {
                if (is_numeric($sKey)) {
                    $sKey = 'Line' . $sKey;
                }
                if (is_array($sValue)) {
                    $oObject = $oRoot->addChild($sKey);
                    $this->__toXml($oObject, $sValue);
                } else {
                    $oRoot->addChild($sKey, $sValue);
                }
            }
        }

        private function getProperties()
        {
            $aModel = array(
                "ErrorMessage" => $this->ErrorMessage,
                "File" => $this->File,
                "Line" => $this->Line,
                "Code" => $this->Code,
                "StackTrace" => $this->StackTrace,

            );

            if ($this->InternalException instanceof \Exception) {
                $aModel['InternalException']['Message'] = $this->InternalException->getMessage();
                $aModel['InternalException']['File'] = $this->InternalException->getFile();
                $aModel['InternalException']['Line'] = $this->InternalException->getLine();
                $aModel['InternalException']['Code'] = $this->InternalException->getCode();
                $aModel['InternalException']['StackTrace'] = explode(PHP_EOL,
                    $this->InternalException->getTraceAsString());
            }

            return $aModel;

        }

        private function toText()
        {
            $oTemplateFile = new File(__DIR__ . '/Exception.text.mustache');
            $oMustache = new \Mustache_Engine();
            $sTemplateContent = $oTemplateFile->loadRaw();
            return $oMustache->render(
                $sTemplateContent,
                [
                    'ErrorMessage' => $this->ErrorMessage,
                    'Line' => $this->Line,
                    'StackTrace' => $this->StackTrace,
                    'File' => $this->File,
                    'Code' => $this->Code,
                    'InternalException' => $this->InternalException,
                    'ExceptionType' => $this->ExceptionType,
                ]
            );
        }
    }

}
