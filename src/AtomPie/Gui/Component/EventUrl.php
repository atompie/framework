<?php
namespace AtomPie\Gui\Component {

    use AtomPie\Boundary\Gui\Component\IAmEventUrl;
    use AtomPie\Html\Tag\Link;
    use AtomPie\Web\Connection\Http\ImmutableUrl;

    class EventUrl implements IAmEventUrl
    {

        private $sEvent;
        private $sUrl;
        private $aParams;

        public function __construct($sUrl, $sEvent = null, $aParams = null)
        {
            $this->sUrl = $sUrl;
            $this->sEvent = $sEvent;
            $this->aParams = $aParams;
        }

        /**
         * @return string
         */
        public function getUrl()
        {
            return $this->sUrl;
        }

        /**
         * @return array
         */
        public function getParams()
        {
            return $this->aParams;
        }

        /**
         * Adds param to url
         *
         * @param $sName
         * @param $sValue
         */
        public function addParam($sName, $sValue)
        {
            $this->aParams[$sName] = $sValue;
        }

        /**
         * Removes param from url
         *
         * @param $sName
         */
        public function removeParam($sName)
        {
            unset($this->aParams[$sName]);
        }

        /**
         * @param $aParams
         */
        public function setParams($aParams)
        {
            $this->aParams = $aParams;
        }

        /**
         * @return string
         */
        public function getEvent()
        {
            return $this->sEvent;
        }

        /**
         * Returns link tag to EndPointEventUrl.
         *
         * @param $sLink
         * @return Link
         */
        public function asLinkTag($sLink)
        {
            return new Link($this, $sLink);
        }

        public function __toString()
        {
            if (is_array($this->aParams)) {
                $sParams = http_build_query($this->aParams, null, ImmutableUrl::PARAM_SEPARATOR);
                return $this->sUrl . $this->sEvent . ImmutableUrl::URL_SEPARATOR . $sParams;
            }

            return $this->sUrl . $this->sEvent;
        }
    }
}
