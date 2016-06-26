<?php
namespace AtomPie\System {

    use AtomPie\Boundary\Core\IRegisterContentProcessors;
    use AtomPie\Boundary\Core\IProcessContent;
    use AtomPie\Boundary\Core\ISerializeModel;
    use AtomPie\System\Dispatch\DispatchException;
    use AtomPie\Web\Boundary\IRecognizeMediaType;
    use Generi\Boundary\IStringable;

    class ContentProcessor implements IProcessContent, IRegisterContentProcessors
    {

        private $aAfterProcessThatConvertEndpointContentToHtml = [];
        private $aBeforeProcessEndpointInit = [];
        private $aFinallyProcessEndpointInit = [];

        public function __construct()
        {
        }

        public function registerAfter($sReturnType, $pClosure)
        {
            $this->aAfterProcessThatConvertEndpointContentToHtml[$sReturnType] = $pClosure;
        }

        public function registerBefore($pClosure)
        {
            $this->aBeforeProcessEndpointInit[] = $pClosure;
        }

        public function registerFinally($sReturnType, $pClosure)
        {
            $this->aFinallyProcessEndpointInit[$sReturnType] = $pClosure;
        }

        /**
         * @param $sClassName
         * @param $aCollection
         * @return bool|callable
         */
        private function getClosureByType($sClassName, &$aCollection)
        {
            if (isset($aCollection[$sClassName])) {
                $pClosure = $aCollection[$sClassName];
                if (is_callable($pClosure)) {
                    return $pClosure;
                }
            }

            return false;
        }

        /**
         * @param $mContent
         * @param $sType
         * @param $aCollection
         * @return bool|callable
         */
        private function getClosureBySubTypeOfContent($mContent, $sType, &$aCollection)
        {
            if ($mContent instanceof $sType) {
                $pClosure = $aCollection[$sType];
                if (is_callable($pClosure)) {
                    return $pClosure;
                }
            }

            return false;
        }

        ////////////////////////////////////////////
        // Converting

        private function convertArrayToXml($aArray, \SimpleXMLElement $oXml)
        {
            foreach ($aArray as $sName => $sValue) {
                if (is_numeric($sName) || is_numeric(substr($sName, 0, 1))) {
                    $sName = (string)$sName;
                    $sName = '_x' . sprintf("%04d", dechex(ord(substr($sName, 0, 1)))) . '_' . substr($sName, 1);
                }
                if (is_array($sValue)) {
                    $this->convertArrayToXml($sValue, $oXml->addChild($sName));
                } else {
                    if (is_object($sValue)) {
                        $this->convertObjectToXml($sValue, $oXml->addChild($sName));
                    } else {
                        $oXml->addChild($sName, htmlspecialchars($sValue));
                    }
                }
            }
            return $oXml->asXML();
        }

        private function convertObjectToXml($array, \SimpleXMLElement $oXml)
        {
            $aProperties = get_object_vars($array);
            foreach ($aProperties as $sName => $sValue) {
                if (is_object($sValue)) {
                    $this->convertObjectToXml($sValue, $oXml->addChild($sName));
                } else {
                    $oXml->addChild($sName, $sValue);
                }
            }
            return $oXml->asXML();
        }

        /**
         * Invokes closures that init dependencies of
         * the processed classes. Most of the time it is the
         * dependency injection container.
         */
        public function processBefore()
        {
            foreach ($this->aBeforeProcessEndpointInit as $pClosure) {
                $pClosure();
            }
        }

        /**
         * Finds and invokes closure from closure repository.
         * Closures are indexed by content class type.
         * If $mContent is not an object it will not be
         * processed aby any closure.
         *
         * @param $mContent
         * @return string
         * @throws DispatchException
         */
        public function processAfter($mContent)
        {
            /*
             * $mContent can be any type of content.
             * Here we are expecting Page or Component.
             */
            return $this->runByClosure($mContent, $this->aAfterProcessThatConvertEndpointContentToHtml);
        }

        /**
         * @param $mContent
         * @param IRecognizeMediaType $oContentType
         * @return array|mixed|string
         * @throws DispatchException
         */
        public function processFinally($mContent, IRecognizeMediaType $oContentType)
        {

            if ($oContentType->isJson()) {

                if ($mContent instanceof ISerializeModel) {
                    $mContent = $this->placeHoldersToArray($mContent->__toModel());
                }

                $mContent = json_encode($mContent);

                if ($mContent === false) {
                    throw new DispatchException('EndPoint of Content-Type: application/json returned data that could not be encoded to JSON string!');
                }

            } else {
                if ($oContentType->isXml()) {

                    if ($mContent instanceof ISerializeModel) {
                        $aDataStructure = $this->placeHoldersToArray($mContent->__toModel());
                        $mContent = $this->convertArrayToXml($aDataStructure, new \SimpleXMLElement(/** @lang XML */
                            '<Root />'));
                    } else {
                        if (is_string($mContent)) {
                            $mContent = sprintf(/** @lang XML */
                                ' <Root><![CDATA[%s]]></Root>', $mContent);
                        } else {
                            if (is_bool($mContent)) {
                                $mContent = sprintf(/** @lang XML */
                                    '<Root>%s</Root>', (string)$mContent);
                            } else {
                                if (is_scalar($mContent)) {
                                    $mContent = sprintf(/** @lang XML */
                                        '<Root>%s</Root>', $mContent);
                                } else {
                                    if (is_array($mContent)) {
                                        /** @noinspection CheckEmptyScriptTag */
                                        $mContent = $this->convertArrayToXml($mContent,
                                            new \SimpleXMLElement('<Root />'));
                                    } else {
                                        if (is_object($mContent)) {
                                            /** @noinspection CheckEmptyScriptTag */
                                            $mContent = $this->convertObjectToXml($mContent,
                                                new \SimpleXMLElement('<Root />'));
                                        } else {
                                            throw new DispatchException('EndPoint of Content-Type: application/xml returned data that could not be serialized to XML');
                                        }
                                    }
                                }
                            }
                        }
                    }

                } else {
                    $mContent = $this->runFinallyByClosure($mContent);
                }
            }

            return $mContent;
        }

        private function placeHoldersToArray($aPlaceHolders)
        {

            $aData = array();
            foreach ($aPlaceHolders as $sKey => $mPlaceHolder) {
                if (is_array($mPlaceHolder) || $mPlaceHolder instanceof \Iterator) {
                    $aData[$sKey] = $this->placeHoldersToArray($mPlaceHolder);
                } else {
                    if ($mPlaceHolder instanceof ISerializeModel) {
                        $aData[$sKey] = $this->placeHoldersToArray($mPlaceHolder->__toModel());
                    } else {
                        if ($mPlaceHolder instanceof \stdClass) {
                            $aData[$sKey] = (array)$mPlaceHolder;
                        } else {
                            if ($mPlaceHolder instanceof IStringable) {
                                $aData[$sKey] = $mPlaceHolder->__toString();
                            } else {
                                $aData[$sKey] = $mPlaceHolder;
                            }
                        }
                    }
                }
            }

            return $aData;

        }

        private function canBeString($sValue)
        {
            if (is_object($sValue) && method_exists($sValue, '__toString')) {
                return true;
            }
            if (is_null($sValue)) {
                return true;
            }
            return is_scalar($sValue);
        }

        /**
         * @param $mContent
         * @return string
         * @throws DispatchException
         */
        private function runFinallyByClosure($mContent)
        {
            $mContent = $this->runByClosure($mContent, $this->aFinallyProcessEndpointInit);

            // All other text/html, application/xhtml+xml, etc.
            if (!$this->canBeString($mContent)) {
                throw new DispatchException('EndPoint of Content-Type: text/html returned object that can not be casted to string! Could not convert array to string. Please return string.');
            }

            return (string)$mContent;
        }

        private function runByClosure($mContent, $aClosureDefinition)
        {

            if (is_object($mContent)) {
                $pClosure = $this->getClosureByType(get_class($mContent), $aClosureDefinition);
                if ($pClosure !== false) {
                    // Hard binding to class type
                    return $pClosure($mContent);
                } else {
                    // Soft binding. It can be a subclass or interface
                    foreach ($aClosureDefinition as $sType => $pClosure) {
                        $pClosure = $this->getClosureBySubTypeOfContent($mContent, $sType, $aClosureDefinition);
                        if (false !== $pClosure) {
                            return $pClosure($mContent);
                        }
                    }
                }
            }
            return $mContent;
        }

    }

}
