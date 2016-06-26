<?php
namespace AtomPie\Html\Form\Field {

    use Generi\Boundary\IEntity;
    use AtomPie\Html\Boundary\IAutoIndex;
    use AtomPie\Html\Boundary\IHaveName;
    use AtomPie\Html\ElementNode;
    use AtomPie\Html\Form\ParamName;

    abstract class AbstractElement extends ElementNode implements IEntity, IHaveName, IAutoIndex
    {
        /**
         * @var ParamName
         */
        private $oName;
        private $sId;

        public function __construct($sTagName, $mName)
        {
            parent::__construct($sTagName);
            if (!is_null($mName)) {
                $this->setName($mName);
                $this->setId($mName);
            }
        }

        ///////////////////////////
        // IName

        /**
         * @param string $mName
         */
        public function setName($mName)
        {
            if ($mName instanceof ParamName) {
                $this->oName = $mName;
            } else {
                list($mName, $iIndex) = ParamName::parse($mName);
                $this->oName = new ParamName($mName, $iIndex);
            }
        }

        /**
         * @return string
         */
        public function getName()
        {
            return $this->oName->__toString();
        }

        /**
         * @return bool
         */
        public function hasName()
        {
            return !is_null($this->oName);
        }

        /**
         * @return ParamName
         */
        public function getParamName()
        {
            return $this->oName;
        }

        ////////////////////////////
        // IAutoIndex

        public function hasIndex()
        {
            return $this->oName instanceof ParamName && $this->oName->hasIndex();
        }

        public function getIndex()
        {
            return $this->oName->getIndex();
        }

        public function setIndex($iIndex)
        {
            $this->oName->setIndex($iIndex);
        }

        public function setAutoIndex($bAutoIndex = true)
        {
            $this->oName->setAutoIndex($bAutoIndex);
        }

        public function hasAutoIndex()
        {
            return $this->oName->hasAutoIndex();
        }

        ///////////////////////////
        // IEntity

        private function getClearId($sId)
        {
            $sId = str_replace('[', '', $sId);
            $sId = str_replace(']', '', $sId);

            return $sId;
        }

        /**
         * @return int
         */
        public function getId()
        {
            return $this->sId;
        }

        /**
         * @param $sId
         * @return void
         */
        public function setId($sId)
        {
            $this->sId = $this->getClearId($sId);
        }

        /**
         * @return bool
         *
         * @Invariant('type'='bool')
         */
        public function hasId()
        {
            return !empty($this->sId);
        }
    }

}