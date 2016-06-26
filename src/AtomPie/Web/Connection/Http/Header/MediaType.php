<?php
namespace AtomPie\Web\Connection\Http\Header {

    use AtomPie\Web\Exception;

    /**
     * Class MediaType is responsible for holding
     * media type parts form Accept header.
     * @package AtomPie\Web\Connection\Http\Header
     */
    class MediaType extends \stdClass
    {

        /**
         * Media type
         *
         * @var string
         */
        public $type;
        /**
         * Media sub type
         *
         * @var string
         */
        public $subType;

        /**
         * Media params.
         *
         * @var array
         */
        public $params;

        /**
         * @return string
         */
        public function getMedia()
        {
            return $this->type . '/' . $this->subType;
        }

        /**
         * @param $sMediaType
         * @param bool $bExplicit
         * @return bool
         */
        public function willYouAccept($sMediaType, $bExplicit = false)
        {
            $oMediaType = MediaType::parseMediaType($sMediaType);
            $aElements = explode(';', $this->getMedia());

            // The same media type and sub type
            if (strtolower($aElements[0]) == strtolower($oMediaType->getMedia())) {
                return true;
            }
            /** @noinspection PhpUnusedLocalVariableInspection */
            list($sHeaderType, $sHeaderSubType) = explode('/', $aElements[0]);

            // Header accepts all types
            if (!$bExplicit && $sHeaderType == '*' && $sHeaderSubType == '*') {
                return true;
            }

            /** @noinspection PhpUnusedLocalVariableInspection */
            list($sCheckedType, $sCheckedSubType) = explode('/', $sMediaType);

            // The same type, subtype does not mather.
            return (!$bExplicit && $sHeaderSubType == '*' && strtolower($sCheckedType) == strtolower($sHeaderType));
        }

        public function __toString()
        {

            $sMedia = $this->getMedia();
            if (isset($this->params)) {
                $aParams = array();
                foreach ($this->params as $sKey => $sValue) {
                    $aParams[] = $sKey . '=' . $sValue;
                }

                if (!empty($aParams)) {
                    $sMedia .= ';' . implode(';', $aParams);
                }
            }

            return $sMedia;
        }

        /**
         * @param $sMediaTypeString
         * @return MediaType
         * @throws Exception
         */
        public static function parseMediaType($sMediaTypeString)
        {
            $aElements = explode(';', $sMediaTypeString);

            $oAcceptMediaType = new MediaType();
            $aExplodedMediaType = explode('/', current($aElements));
            if (count($aExplodedMediaType) == 2) {
                list($sType, $sSubtype) = $aExplodedMediaType;
                $oAcceptMediaType->type = trim($sType);
                $oAcceptMediaType->subType = trim($sSubtype);
            } else {
                throw new Exception('Incorrect media type.');
            }

            $oAcceptMediaType->params = array();
            while (next($aElements)) {
                $aExplodedParam = explode('=', current($aElements));
                if (count($aExplodedParam) == 2) {
                    list($sName, $sValue) = $aExplodedParam;
                    $oAcceptMediaType->params[trim($sName)] = trim($sValue);
                } else {
                    throw new Exception('Incorrect media type param.');
                }
            }
            return $oAcceptMediaType;
        }
    }

}
