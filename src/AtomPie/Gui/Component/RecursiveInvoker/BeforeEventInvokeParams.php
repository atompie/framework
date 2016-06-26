<?php
namespace AtomPie\Gui\Component\RecursiveInvoker {

    use AtomPie\Boundary\Gui\Component\IHaveEvents;

    class BeforeEventInvokeParams
    {

        /**
         * @var IHaveEvents
         */
        private $oComponent;
        private $sEventMethod;

        public function __construct(IHaveEvents $oComponent, $sEventMethod)
        {
            $this->oComponent = $oComponent;
            $this->sEventMethod = $sEventMethod;
        }

        /**
         * @return mixed
         */
        public function getComponent()
        {
            return $this->oComponent;
        }

        /**
         * @return mixed
         */
        public function getEvent()
        {
            return $this->sEventMethod;
        }
    }

}
