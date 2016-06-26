<?php
namespace AtomPie\Gui\Component {

    use Generi\Boundary\ICanBeIdentified;
    use Generi\Type;
    use AtomPie\Boundary\Gui\Component\IAmComponentParam;
    use AtomPie\Web\Connection\Http\Url\Param;

    class Params
    {

        /**
         * @var ICanBeIdentified
         */
        private $oComponent;

        /**
         * @var Type
         */
        private $oComponentType;

        /**
         * @var array|Param[]
         */
        private $aParams;

        /**
         * Example
         *
         * new Params(
         *      new Param\Sorting('Column1'),
         *      new Param\Page(1)
         * );
         */
        final public function __construct()
        {
            $this->aParams = func_get_args();
        }

        public function setComponent(ICanBeIdentified $oComponent)
        {
            $this->oComponent = $oComponent;
        }

        /**
         * @param Type $oComponentType
         */
        public function setComponentType(Type $oComponentType)
        {
            $this->oComponentType = $oComponentType;
        }

        /**
         * @return array
         */
        public function __toArray()
        {
            $aToArray = array();
            foreach ($this->aParams as $oParam) {

                if ($oParam instanceof IAmComponentParam && $oParam->hasComponentContext()) {

                    // Context from this param
                    $sContext = $oParam->getComponentContext();
                    $aToArray[$oParam->getName()][$sContext] = $oParam->getValue();

                } else {
                    if (isset($this->oComponent) && $oParam instanceof IAmComponentParam) {

                        // Context from params collection
                        $oContext = new ComponentParamSessionKey($this->oComponent);
                        $sContext = $oContext->__toString();
                        $aToArray[$oParam->getName()][$sContext] = $oParam->getValue();

                    } else {
                        if (isset($this->oComponentType) && $oParam instanceof IAmComponentParam) {
                            $aToArray[$oParam->getName()][$this->oComponentType->getName()] = $oParam->getValue();
                        } else {
                            $aToArray[$oParam->getName()] = $oParam->getValue();
                        }
                    }
                }
            }
            return $aToArray;
        }

    }

}
