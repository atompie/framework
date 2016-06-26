<?php
namespace AtomPie\Web\Boundary;

interface IPersistParamState
{

    /**
     * Loads state from session.
     *
     * @param $sParamName
     * @param string $sContext
     * @return mixed|null
     */
    public function loadState($sParamName, $sContext = 'global-context');

    /**
     * Saves state to session.
     *
     * @param IAmRequestParam $oParam
     * @param null $sAs
     * @param string $sContext
     * @return
     */
    public function saveState(IAmRequestParam $oParam, $sAs = null, $sContext = 'global-context');

    /**
     * Removes param value from session.
     *
     * If you set $bRemoveAllValuesRegardlessContext to TRUE
     * all param values will be removed regardless the context.
     *
     * So if there is param A with 10 values for different components
     * all values will be removed.
     *
     * @param $sParamName
     * @param bool $bRemoveAllValuesRegardlessContext
     * @param string $sContext
     */
    public function removeState($sParamName, $bRemoveAllValuesRegardlessContext = false, $sContext = 'global-context');

    /**
     * @param $sParamName
     * @return bool
     */
    public function hasState($sParamName);
}