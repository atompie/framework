<?php
namespace AtomPie\Core {

    use AtomPie\Boundary\Core\IAmApplicationConfigDefinition;
    use AtomPie\Boundary\Core\IAmFrameworkConfig;
    use AtomPie\Boundary\System\IAmRouter;
    use AtomPie\Core\Config\ApplicationConfigProvider;
    use AtomPie\Web\Boundary\IAmEnvironment;

    final class FrameworkConfig implements IAmFrameworkConfig
    {

        const VARIABLE_ENV = 'ATOMPIE_ENV';
        const DEFAULT_ENV = 'default';

        private $aEndPointNamespaces = [];
        private $aEndPointClasses = [];
        private $aEventNamespaces = [];
        private $aEventClasses = [];
        private $sRootFolder = __DIR__;
        private $sViewFolder = __DIR__;

        /**
         * @var string
         */
        private $sDefaultEndPoint;

        /**
         * @var
         */
        private $oAppConfig;

        /**
         * @var IAmRouter
         */
        private $oRouter;

        public function __construct(
            IAmEnvironment $oEnvironment,
            IAmRouter $oRouter,
            IAmApplicationConfigDefinition $oConfigDefinition,
            $sRootFolder,
            $sViewFolder,
            array $aEndPointNamespaces = [],
            array $aEventNamespaces = [],
            array $aEndPointClasses = [],
            array $aEventClasses = [],
            $sDefaultEndPoint = 'Main'
        ) {

            $this->sRootFolder = $sRootFolder;
            $this->sViewFolder = $sViewFolder;
            $this->sDefaultEndPoint = $sDefaultEndPoint;
            $this->aEndPointNamespaces = $aEndPointNamespaces;
            $this->aEventNamespaces = $aEventNamespaces;
            $this->aEndPointClasses = $aEndPointClasses;
            $this->aEventClasses = $aEventClasses;
            $this->oRouter = $oRouter;
            $this->oAppConfig = (new ApplicationConfigProvider($oConfigDefinition, $oEnvironment->getEnv()))->provide();
        }

        /**
         * @return array
         */
        public function getEndPointNamespaces()
        {
            return $this->aEndPointNamespaces;
        }

        /**
         * @return array
         */
        public function getEndPointClasses()
        {
            return $this->aEndPointClasses;
        }

        /**
         * @param $sNamespace
         * @return bool
         */
        public function hasEndPointNamespace($sNamespace) {
            return in_array($sNamespace, $this->aEndPointNamespaces);
        }

        /**
         * @param $sNamespace
         */
        public function prependEndPointNamespace($sNamespace) {
            array_unshift(
                $this->aEndPointNamespaces,
                $sNamespace
            );
        }

        /**
         * @return array
         */
        public function getEventNamespaces()
        {
            return $this->aEventNamespaces;
        }

        /**
         * @return array
         */
        public function getEventClasses()
        {
            return $this->aEventClasses;
        }

        /**
         * @return string
         */
        public function getRootFolder()
        {
            return $this->sRootFolder;
        }

        /**
         * @return string
         */
        public function getViewFolder()
        {
            return $this->sViewFolder;
        }

        /**
         * @return string
         */
        public function getDefaultEndPoint()
        {
            return $this->sDefaultEndPoint;
        }

        /**
         * @return mixed
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
    }

}
