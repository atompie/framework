<?php
namespace AtomPie\Core {

    use AtomPie\Boundary\Core\IAmApplicationConfigSwitcher;
    use AtomPie\Boundary\Core\IAmFrameworkConfig;
    use AtomPie\Boundary\Core\ISetUpContentProcessor;
    use AtomPie\Boundary\System\IAmEnvVariable;
    use AtomPie\Boundary\System\IAmRouter;
    use AtomPie\Boundary\System\IHandleException;
    use AtomPie\Boundary\System\IRunAfterMiddleware;
    use AtomPie\Boundary\System\IRunBeforeMiddleware;
    use AtomPie\Boundary\Config\IAmApplicationConfig;
    use AtomPie\System\ContractFillers;
    use AtomPie\System\EndPointConfig;
    use AtomPie\System\EventConfig;
    use AtomPie\Web\Boundary\IAmEnvironment;

    final class FrameworkConfig implements IAmFrameworkConfig
    {

        /**
         * @var string
         */
        private $sDefaultEndPoint;

        /**
         * @var IAmApplicationConfig
         */
        private $oAppConfig;

        /**
         * @var IAmRouter
         */
        private $oRouter;

        /**
         * @var ContractFillers
         */
        private $oContractFillers;
        
        /**
         * @var IRunBeforeMiddleware[] | IRunAfterMiddleware[]
         */
        private $aMiddleware;
        
        /**
         * @var EndPointConfig
         */
        private $oEndPointConfig;
        
        /**
         * @var EventConfig
         */
        private $oEventConfig;
        /**
         * @var ISetUpContentProcessor[]
         */
        private $aContentProcessors;
        
        /**
         * @var IHandleException
         */
        private $oErrorRenderer;

        public function __construct(
            $sDefaultEndPoint,
            EndPointConfig $oEndPointConfig,
            IAmApplicationConfigSwitcher $oConfigSwitcher,
            IAmEnvironment $oEnvironment,
            array $aContractFillers = [],
            array $aMiddleware = [],
            array $aContentProcessors = [],
            IHandleException $oErrorRenderer = null,
            EventConfig $oEventConfig = null,
            IAmRouter $oRouter = null
        ) {

            $this->sDefaultEndPoint = $sDefaultEndPoint;
            $this->oRouter = $oRouter;
            $this->oAppConfig = $this->provide($oConfigSwitcher, $oEnvironment->getEnv());
            $this->oContractFillers = new ContractFillers($aContractFillers);
            $this->aMiddleware = $aMiddleware;
            $this->oEndPointConfig = $oEndPointConfig;
            $this->oEventConfig = ($oEventConfig !== null) ? $oEventConfig : new EventConfig();
            $this->aContentProcessors = $aContentProcessors;
            $this->oErrorRenderer = $oErrorRenderer;
        }

        /**
         * Override in order to change the way you distinguish
         * dev and production environments.
         *
         * @param IAmApplicationConfigSwitcher $oConfigSwitcher
         * @param IAmEnvVariable $oEnvVariable
         * @return IAmApplicationConfig
         * @throws \Exception
         */
        private function provide(IAmApplicationConfigSwitcher $oConfigSwitcher, IAmEnvVariable $oEnvVariable)
        {
            $sLocalConfigClass = $oConfigSwitcher->localConfig();
            if ($sLocalConfigClass !== null) {
                // Local config
                return $this->getConfig($sLocalConfigClass, $oEnvVariable);
            }

            // Default config
            return $this->getConfig($oConfigSwitcher->defaultConfig(), $oEnvVariable);
        }

        /**
         * @param $sLocalConfigClass
         * @param IAmEnvVariable $oEnvVariable
         * @return mixed
         * @throws \Exception
         */
        private function getConfig($sLocalConfigClass, $oEnvVariable)
        {
            if (!class_exists($sLocalConfigClass)) {
                throw new \Exception(
                    sprintf(
                        'Class [%s] does not exist. Config must provide ApplicationConfig class that exists.',
                        $sLocalConfigClass
                    )
                );
            }

            $oConfig = new $sLocalConfigClass($oEnvVariable);

            if (!$oConfig instanceof IAmApplicationConfig) {
                throw new \Exception('Config must provide class that is child of \AtomPie\Boundary\Core\IAmApplicationConfig class.');
            }

            return $oConfig;
        }


        /**
         * @return array
         */
        public function getEndPointNamespaces()
        {
            return $this->oEndPointConfig->getEndPointNamespaces()->__toArray();
        }

        /**
         * @return array
         */
        public function getEndPointClasses()
        {
            return $this->oEndPointConfig->getEndPointClasses()->__toArray();
        }

        /**
         * @return array
         */
        public function getEventNamespaces()
        {
            return $this->oEventConfig->getEventNamespaces()->__toArray();
        }

        /**
         * @return array
         */
        public function getEventClasses()
        {
            return $this->oEndPointConfig->getEndPointClasses()->__toArray();
        }

        /**
         * @return string
         */
        public function getDefaultEndPoint()
        {
            return $this->sDefaultEndPoint;
        }

        /**
         * @return IAmApplicationConfig
         */
        public function getAppConfig()
        {
            return $this->oAppConfig;
        }

        /**
         * @return IAmRouter
         */
        public function getRouter()
        {
            return $this->oRouter;
        }

        /**
         * @return ContractFillers
         */
        public function getContractsFillers()
        {
            return $this->oContractFillers;
        }

        /**
         * @return IRunAfterMiddleware[]|IRunBeforeMiddleware[]
         */
        public function getMiddleware()
        {
            return $this->aMiddleware;
        }

        /**
         * @return ISetUpContentProcessor[]
         */
        public function getContentProcessors()
        {
            return $this->aContentProcessors;
        }

        /**
         * @return IHandleException
         */
        public function getErrorHandler()
        {
            return $this->oErrorRenderer;
        }
    }

}
