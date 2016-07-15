<?php
namespace AtomPiePhpUnitTest;

use AtomPie\Boundary\Core\IAmApplicationConfigSwitcher;
use AtomPie\Boundary\System\IAmEnvVariable;

class ApplicationConfigSwitcher implements IAmApplicationConfigSwitcher {

    /**
     * @var IAmEnvVariable
     */
    private $oEnvVariable;

    public function __construct(IAmEnvVariable $oEnvVariable)
    {
        $this->oEnvVariable = $oEnvVariable;
    }

    /**
     * Returns default config class type. One that is used
     * if no class is returned by localConfig() method.
     *
     * @return string
     */
    public function defaultConfig()
    {
        return ApplicationConfig::class;
    }

    /**
     * Returns local config class to be used depending on
     * environment used. In order to change how the environment is
     * determined override provide method.
     *
     * @return string
     */
    public function localConfig()
    {
        return $this->oEnvVariable->has('environment')
            ? $this->oEnvVariable->get('environment')
            : null;
    }
}