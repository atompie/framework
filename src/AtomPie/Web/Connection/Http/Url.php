<?php
namespace AtomPie\Web\Connection\Http {

    use AtomPie\Web\Boundary\IAmRequestParam;
    use AtomPie\Web\Boundary\IAmUrl;

    /**
     * Class responsibility:  Handles http url, Handles http url parameters.
     *
     * Use this class to prepare object oriented http url.
     *
     *
     *  Example:
     * <pre>
     *  $oUrl = new \Web\Connection\Http\Url();
     *  $oUrl->setUrl('http://localhost/cont/action');
     *  $oUrl->addHttpParam(new \Web\Connection\Http\Url\Param('id1',1));
     *  $oUrl->addKeyValueParam('id2',2);
     *
     *  echo $oUrl;
     *  </pre>
     */
    class Url extends ImmutableUrl implements IAmUrl
    {

        public function setAnchor($sAnchor = null)
        {
            $this->sAnchor = $sAnchor;
        }

        /**
         * Sets url parameters. Pass array of key, value to set parameters.
         *
         * @param array $aParams
         */
        public function setParams($aParams)
        {
            $this->aParams = $aParams;
        }


        /**
         * Sets url.
         *
         * @param string $sUrl
         */
        public function setUrl($sUrl)
        {
            $this->sUrl = $sUrl;
        }

        /**
         * Adds key, value pair as url parameters.
         *
         * @param string $sKey
         * @param string $sValue
         */
        public function addKeyValueParam($sKey, $sValue)
        {
            $this->aParams[$sKey] = $sValue;
        }

        /**
         * Adds url parameter as Web\Connection\Http\Url\Param class.
         *
         * @param IAmRequestParam $oParam
         */
        public function addHttpParam(IAmRequestParam $oParam)
        {
            $this->aParams[$oParam->getName()] = $oParam->getValue();
        }

        /**
         * Removes parameter from Web\Connection\Http\Url
         *
         * @param string $sKey
         */
        public function removeParam($sKey)
        {
            unset($this->aParams[$sKey]);
        }

    }

}