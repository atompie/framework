<?php
namespace AtomPie\Web\Connection\Http\Header {

    use AtomPie\Web\Exception;

    /**
     * Note : Code is released under the GNU LGPL
     *
     * Please do not change the header of this file
     *
     * This library is free software; you can redistribute it and/or modify it under the terms of the GNU
     * Lesser General Public License as published by the Free Software Foundation; either version 2 of
     * the License, or (at your option) any later version.
     *
     * This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
     * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
     *
     * See the GNU Lesser General Public License for more details.
     */

    /**
     * Based on code written by
     * @author      Pierrick Charron <pierrick@webstart.fr>
     * @author      Risto Kowaczewski
     */
    class AcceptMediaTypesArray extends \ArrayObject
    {

        /**
         * Constructor
         *
         * @param string $sHeader Value of the Accept header
         */
        public function __construct($sHeader)
        {
            $aAcceptedTypes = $this->parse($sHeader);
            usort($aAcceptedTypes, array($this, 'compare'));
            parent::__construct($aAcceptedTypes);
        }

        /**
         * Parse the accept header and return an array containing
         * all the information about the Accepted types
         *
         * @param string $sData Value of the Accept header
         * @return array
         * @throws Exception
         */
        private function parse($sData)
        {
            $aCollectionOfMediaTypes = array();
            $aSetOfMediaTypes = explode(',', $sData);
            foreach ($aSetOfMediaTypes as $aItem) {
                $aCollectionOfMediaTypes[] = MediaType::parseMediaType($aItem);
            }
            return $aCollectionOfMediaTypes;
        }

        /**
         * Compare two Accepted types with their parameters to know
         * if one media type should be used instead of an other
         *
         * @param MediaType $a The first media type and its parameters
         * @param MediaType $b The second media type and its parameters
         * @return int
         */
        private function compare(MediaType $a, MediaType $b)
        {
            $a_q = isset($a->params['q']) ? floatval($a->params['q']) : 1.0;
            $b_q = isset($b->params['q']) ? floatval($b->params['q']) : 1.0;
            if ($a_q === $b_q) {
                $a_count = count($a->params);
                $b_count = count($b->params);
                if ($a_count === $b_count) {
                    if ($r = $this->compareSubType($a->subType, $b->subType)) {
                        return $r;
                    } else {
                        return $this->compareSubType($a->type, $b->type);
                    }
                } else {
                    return $a_count < $b_count;
                }
            } else {
                return $a_q < $b_q;
            }
        }

        /**
         * Compare two subtypes
         *
         * @param string $a First subtype to compare
         * @param string $b Second subtype to compare
         * @return int
         */
        private function compareSubType($a, $b)
        {
            if ($a === '*' && $b !== '*') {
                return 1;
            } elseif ($b === '*' && $a !== '*') {
                return -1;
            } else {
                return 0;
            }
        }

        /**
         * Returns array of media types.
         *
         * @return array
         */
        public function getMediaTypes()
        {
            /** @var MediaType $oMediaType */
            $aMediaTypes = array();
            foreach ($this as $oMediaType) {
                $aMediaTypes[] = $oMediaType->getMedia();
            }
            return $aMediaTypes;
        }


    }

}
