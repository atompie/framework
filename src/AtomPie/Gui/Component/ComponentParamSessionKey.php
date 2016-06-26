<?php
namespace AtomPie\Gui\Component {

    use Generi\Boundary\ICanBeIdentified;

    class ComponentParamSessionKey
    {

        const SEPARATOR = '.';

        private $sComponentName;
        private $sComponentType;

        public function __construct(ICanBeIdentified $oComponent)
        {
            $this->sComponentName = $oComponent->getName();
            $this->sComponentType = $oComponent->getType()->getName();
        }

        public function __toString()
        {
            return $this->sComponentType . self::SEPARATOR . $this->sComponentName;
        }

    }

}
