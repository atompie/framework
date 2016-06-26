<?php
namespace AtomPie\Gui\Component\Template {

    use AtomPie\Gui\Component\Exception;
    use AtomPie\Gui\Component\Part;

    class PlaceHolder
    {

        const COLLECTION = 1;
        const ITEM = 2;

        private $sName;
        /**
         * @var Part
         */
        private $oContext;
        /**
         * @var Part
         */
        private $oComponent;

        private $sClassName;

        private $iType;

        public function __construct(Part $oContext, $sName)
        {
            $this->oContext = $oContext;
            $this->sName = $sName;
        }

        /**
         * Defines place holder content. Pass component class name
         *
         *
         * @param $sClassName
         * @return $this
         * @throws Exception
         */
        public function is($sClassName)
        {
            $this->sClassName = $sClassName;
            $this->iType = self::ITEM;
            $this->oComponent = new $sClassName($this->sName, $this->oContext);
            if (!$this->oComponent instanceof Part) {
                throw new Exception('Only components can be created with component method.');
            }
            $this->oContext->__set($this->sName, $this->oComponent);
            return $this;
        }

        public function has($sClassName)
        {
            $this->sClassName = $sClassName;
            $this->iType = self::COLLECTION;
            $this->oContext->__set($this->sName, []);
            return $this;
        }

        /**
         * @param array $aCollection
         * @throws Exception
         */
        public function filledWith(array $aCollection)
        {

            if (!isset($this->iType) || !isset($this->sClassName)) {
                throw new Exception('Set component type before filling it with data. Use IS method or HAS method first.');
            }

            $sClassName = $this->sClassName;
            if ($this->iType == self::ITEM) {
                $this->oComponent->setPlaceHolders($aCollection);
            } else {

                $aObjectCollection = [];
                if (!empty($aCollection)) {

                    $i = 0;
                    foreach ($aCollection as $aItem) {
                        /** @var Part $oComponent */
                        $oComponent = new $sClassName($this->sName . $i++, $this->oContext);
                        $oComponent->setPlaceHolders($aItem);
                        $aObjectCollection[] = $oComponent;
                    }
                }
                $this->oContext->__set($this->sName, $aObjectCollection);

            }

        }

    }

}
