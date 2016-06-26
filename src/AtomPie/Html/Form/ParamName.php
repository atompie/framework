<?php
namespace AtomPie\Html\Form {

    use AtomPie\Html\Boundary\IAutoIndex;
    use AtomPie\Html\Boundary\IHaveName;
    use AtomPie\Html\Exception;

    class ParamName implements IHaveName, IAutoIndex
    {

        private $sName;
        private $iIndex;
        private $bAutoIndex;

        public function __construct($sName, $iIndex = null)
        {

            if (is_numeric($sName)) {
                $sName = (string)$sName;
            }

            if (!is_string($sName)) {
                throw new Exception('Param name must be string!');
            }

            $this->sName = $sName;
            $this->iIndex = $iIndex;
        }

        public function __toString()
        {
            return $this->getName();
        }

        public function addSuffix($sSuffix)
        {
            $this->sName .= $sSuffix;
            return $this;
        }

        public function addPrefix($sSuffix)
        {
            $this->sName = $sSuffix . $this->sName;
            return $this;
        }

        /////////////////////////////
        // IAutoIndex

        public function hasIndex()
        {
            return isset($this->iIndex);
        }

        public function getIndex()
        {
            return $this->iIndex;
        }

        public function setIndex($iIndex)
        {
            $this->iIndex = $iIndex;
        }

        public function setAutoIndex($bAutoIndex = true)
        {
            $this->bAutoIndex = $bAutoIndex;
        }

        public function hasAutoIndex()
        {
            return $this->bAutoIndex;
        }

        ///////////////////////////
        // IName

        /**
         * (non-PHPdoc)
         * @see INamable::setName()
         * @param string $sName
         */
        public function setName($sName)
        {
            $this->sName = $sName;
        }

        /**
         * (non-PHPdoc)
         * @see INamable::getName()
         */
        public function getName()
        {
            if ($this->hasIndex()) {
                return $this->sName . '[' . $this->getIndex() . ']';
            } else {
                if ($this->hasAutoIndex()) {
                    return $this->sName . '[]';
                }
            }

            return $this->sName;
        }

        /**
         * (non-PHPdoc)
         * @see INamable::hasName()
         */
        public function hasName()
        {
            return !is_null($this->sName);
        }

        ///////////////////////////

        public function getNameWithoutIndex()
        {
            return $this->sName;
        }

        public static function parse($sName)
        {
            if (preg_match('/^([^\[\]]+)\[([^\[\]]*)\]$/', $sName, $aMatches)) {
                return array($aMatches[1], $aMatches[2]);
            } else {
                return array($sName, null);
            }
        }
    }
}