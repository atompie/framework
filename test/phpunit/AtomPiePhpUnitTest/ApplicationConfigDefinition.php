<?php
namespace AtomPiePhpUnitTest;

use AtomPie\Boundary\Core\IAmApplicationConfigDefinition;
use AtomPie\Boundary\System\IAmEnvVariable;

class ApplicationConfigDefinition implements IAmApplicationConfigDefinition {

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
     * if no class is returned by getLocalConfig() method.
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
    public function getLocalConfig()
    {
        return $this->oEnvVariable->has('environment')
            ? $this->oEnvVariable->get('environment')
            : null;
    }
}