<?php
namespace AtomPie\Web\Connection\Http {

    class ParamConverter
    {

        private $aParams = array();

        public function convertArray($aDataInArray, $sPrefix = '')
        {
            foreach ($aDataInArray as $sKey => $mData) {
                if (is_array($mData)) {
                    $this->convertArray($mData, '[' . $sKey . ']');
                } else {
                    $this->aParams[$sPrefix . '[' . $sKey . ']'] = $mData;
                }
            }

            return $this->aParams;
        }
    }

}

