<?php
namespace AtomPie\Core\Config {

    use AtomPie\Boundary\Core\IAmApplicationConfigDefinition;
    use AtomPie\Boundary\System\IAmEnvVariable;
    use AtomPie\Core\ApplicationConfig;

    class ApplicationConfigProvider
    {
        /**
         * @var \AtomPie\System\Environment\EnvVariable
         */
        protected $oEnvVariable;
        /**
         * @var IAmApplicationConfigDefinition
         */
        private $oDefinition;

        public function __construct(IAmApplicationConfigDefinition $oDefinition, IAmEnvVariable $oEnvVariable)
        {
            $this->oEnvVariable = $oEnvVariable;
            $this->oDefinition = $oDefinition;
        }

        /**
         * Override in order to change the way you distinguish
         * dev and production environments.
         *
         * @return ApplicationConfig
         * @throws \Exception
         */
        public function provide()
        {

            $sLocalConfigClass = $this->oDefinition->getLocalConfig();
            if ($sLocalConfigClass !== null) {
                // Local config
                return $this->getConfig($sLocalConfigClass);
            }
            
            // Default config
            return $this->getConfig($this->oDefinition->defaultConfig());
        }

        /**
         * @param $sLocalConfigClass
         * @return mixed
         * @throws \Exception
         */
        private function getConfig($sLocalConfigClass)
        {
            if (!class_exists($sLocalConfigClass)) {
                throw new \Exception(
                    sprintf(
                        'Class [%s] does not exist. Config must provide ApplicationConfig class that exists.',
                        $sLocalConfigClass
                    )
                );
            }

            $oConfig = new $sLocalConfigClass($this->oEnvVariable);

            if (!$oConfig instanceof ApplicationConfig) {
                throw new \Exception('Config must provide class that is child of \AtomPie\Core\ApplicationConfig class.');
            }

            return $oConfig;
        }

    }

}
