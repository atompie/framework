<?php
namespace AtomPie\View {

    use AtomPie\View\Boundary\ICanBeRendered;

    class CollectionItem
    {

        /**
         * @var ICanBeRendered
         */
        private $aProperties;
        private $sCollectionName;
        private $sItemName;
        /**
         * @var null
         */
        private $oComponent;

        public function __construct($sCollectionName, $sItemName, array $aProperties, $oComponent = null)
        {
            $this->aProperties = $aProperties;
            $this->sCollectionName = $sCollectionName;
            $this->sItemName = $sItemName;
            $this->oComponent = $oComponent;
        }

        /**
         * @return array
         */
        public function getProperties()
        {
            return $this->aProperties;
        }

        /**
         * @return mixed
         */
        public function getCollectionName()
        {
            return $this->sCollectionName;
        }

        /**
         * @return mixed
         */
        public function getItemName()
        {
            return $this->sItemName;
        }

        /**
         * @return ICanBeRendered
         */
        public function getComponent()
        {
            return $this->oComponent;
        }
    }

}
