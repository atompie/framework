<?php
namespace AtomPie\System;

use AtomPie\Boundary\System\IAmContractFiller;

class ContractFillers
{

    /**
     * @var IAmContractFiller[]
     */
    private $aContractFillers;

    public function __construct(array $aContractFillers)
    {
        $this->aContractFillers = $aContractFillers;
    }

    /**
     * Returns null if did not found any contract fillers.
     *
     * @param $sInterfaceName
     * @param $sClassName
     * @param $sMethodName
     * @return null|string
     */
    public function getContractFillerFor($sInterfaceName, $sClassName, $sMethodName)
    {
        $sGlobalContractFillerClassName = null;
        $sClassContractFillerClassName = null;
        $sClassAndMethodContractFillerClassName = null;
        foreach ($this->aContractFillers as $oContract) {
            if($oContract->isForContract($sInterfaceName)) {

                if($oContract->isConstrainedToClassAndMethod($sClassName, $sMethodName)) {
                    $sClassAndMethodContractFillerClassName = $oContract->getContractFiller();
                } else if($oContract->isConstrainedToClass($sClassName)) {
                    $sClassContractFillerClassName = $oContract->getContractFiller();
                } else if($oContract->isGlobal()) {
                    $sGlobalContractFillerClassName = $oContract->getContractFiller();
                }

            }
        }

        if(isset($sClassAndMethodContractFillerClassName)) {
            return $sClassAndMethodContractFillerClassName;
        }

        if(isset($sClassContractFillerClassName)) {
            return $sClassContractFillerClassName;
        }

        if(isset($sGlobalContractFillerClassName)) {
            return $sGlobalContractFillerClassName;
        }

        return null;
    }

}
