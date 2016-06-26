<?php
namespace AtomPie\Web\Connection\Http\Url {

    use Generi\Object;
    use AtomPie\I18n\Label;
    use AtomPie\Web\Boundary\IAmRequestParam;
    use AtomPie\Web\Connection\Http\Url\Param\IConstrain;
    use AtomPie\Web\Connection\Http\Url\Param\IForceStrictType;
    use AtomPie\Web\Connection\Http\Url\Param\ParamException;
    use AtomPie\Web\Exception;

    /**
     * Class Param is a representation of request parameter that is always
     * send in form of a string. No validation is applied.
     * <br />
     * Examples of ArrayOfValues type of parameter:
     * URL encoded: <pre class="code">Name=Value+1</pre>
     * JSON: <pre class="code">{"Name": "Value 1"}</pre>
     */
    class Param extends Object implements \ArrayAccess, IAmRequestParam
    {

        protected $sValue;
        protected $sName;

        /**
         * @param string $sName
         * @param string|null $sValue
         * @throws ParamException
         */
        public function __construct($sName, $sValue = null)
        {

            if (!is_string($sName)) {
                throw new ParamException(new Label('Param name must be string.'));
            }

            $this->sName = $sName;
            $this->sValue = $this->getSanitizedValue($sValue);
            if (!$this->isNull()) {
                $this->checkConstrains();
            }
        }

        /**
         * Moves value of the param to array indexed with index.
         *
         * Eg.
         * I have param a = 1
         * I index value with index z
         * Then I have a[z]=1.
         *
         * @param $sIndex
         */
        public function indexValue($sIndex)
        {
            $this->sValue[$sIndex] = $this->sValue;
        }

        /**
         * @return bool
         */
        public function isNull()
        {
            return null === $this->sValue;
        }

        /**
         * @return bool
         */
        public function isArray()
        {
            return is_array($this->sValue);
        }

        /**
         * @return bool
         */
        public function isEmpty()
        {
            return empty($this->sValue);
        }

        /**
         * @return string
         */
        public function getName()
        {
            return $this->sName;
        }

        /**
         * @return mixed
         */
        public function getValue()
        {
            return $this->sValue;
        }

        /**
         * @return string
         */
        public function __toString()
        {
            return http_build_query($this->__toArray());
        }

        /**
         * @return string
         */
        public function __toJson()
        {
            return json_encode($this->__toArray());
        }

        public function __toIndexedArray()
        {
            $aReturn = array();
            $aParts = explode('&amp;', http_build_query($this->__toArray(), null, '&amp;'));
            foreach ($aParts as $sPart) {
                list($sName, $sValue) = explode('=', $sPart);
                $aReturn[rawurldecode($sName)] = $sValue;
            }
            return $aReturn;
        }

        /**
         * @return array
         */
        public function __toArray()
        {
            return array($this->sName => $this->sValue);
        }

        private function getSanitizedValue($sValue)
        {

            if (!is_array($sValue) && !is_null($sValue) && !empty($sValue)) {

                $sSanitizedValue = $this->sanitize($sValue);

                if (false == $sSanitizedValue) {
                    throw new ParamException(sprintf(new Label('Invalid param [%s] value.'), $this->getName()));
                }

                return $sSanitizedValue;

            } else {

                return $sValue;

            }

        }

        private function sanitize($sValue)
        {
            return filter_var($sValue, FILTER_SANITIZE_STRING);
        }

        private function checkConstrains()
        {
            if ($this instanceof IConstrain) {
                if (!$this->validate()) {
                    /** @var $this Param */
                    throw new ParamException(sprintf(new Label('Parameter [%s] has not passed its constrain rules.'),
                        $this->getName()));
                }
            }

            if ($this instanceof IForceStrictType) {
                /** @var $this Param | IForceStrictType */
                $this->sValue = $this->castValue();
            }
        }

        ////////////////////////////
        // ArrayAccess

        public function offsetExists($sOffset)
        {
            return $this->isArray() && isset($this->sValue[$sOffset]);
        }

        public function offsetGet($sOffset)
        {
            if (!$this->isArray()) {
                throw new Exception(sprintf(new Label('[%s:Http\Url\Param] value is not array.'), $this->sName));
            }
            return $this->sValue[$sOffset];
        }

        public function offsetSet($sOffset, $mValue)
        {
            throw new Exception(sprintf(new Label('Can not set [%s:Http\Url\Param]. Read only.'), $this->sName));
        }

        public function offsetUnset($sOffset)
        {
            unset($this->sValue[$sOffset]);
        }

    }

}
