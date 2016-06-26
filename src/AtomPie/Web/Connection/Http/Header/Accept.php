<?php
namespace AtomPie\Web\Connection\Http\Header {

    use AtomPie\Web\Connection\Http\Header;

    class Accept extends Header
    {

        /**
         * @var MediaType[] | AcceptMediaTypesArray
         */
        private $oMediaTypesArray;

        public function __construct($sValue)
        {
            parent::__construct(Header::ACCEPT, $sValue);
            $this->oMediaTypesArray = new AcceptMediaTypesArray($sValue);
        }

        /**
         * @param $sMimeType
         * @param bool $bExplicit
         * @return bool
         */
        public function willYouAcceptMediaType($sMimeType, $bExplicit = false)
        {
            foreach ($this->oMediaTypesArray as $oMediaType) {
                if ($oMediaType->willYouAccept($sMimeType, $bExplicit)) {
                    return true;
                }
            }
            return false;
        }

        /**
         * @param $sMimeType
         * @return null|MediaType
         */
        public function getMediaType($sMimeType)
        {
            foreach ($this->oMediaTypesArray as $oMediaType) {
                if ($oMediaType->getMedia() == $sMimeType) {
                    return $oMediaType;
                }
            }
            return null;
        }

        /**
         * @return AcceptMediaTypesArray|MediaType[]
         */
        public function getMediaTypesByPriority()
        {
            return $this->oMediaTypesArray;
        }

    }

}
