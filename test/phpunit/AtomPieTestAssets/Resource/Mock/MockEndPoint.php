<?php
namespace AtomPieTestAssets\Resource\Mock {

    use AtomPie\AnnotationTag\EndPoint;
    use AtomPie\Core\FrameworkConfig;
    use AtomPie\System\IO\File;
    use AtomPie\Web\Connection\Http\Header\Status;
    use AtomPie\Web\Connection\Http\Url\Param;
    use WorkshopTest\Resource\Config\Config;

    class DependencyWithOtherGlobalDependency {
        /**
         * @var FrameworkConfig
         */
        private $oConfig;

        public function __construct(FrameworkConfig $oConfig)
        {
            $this->oConfig = $oConfig;
        }

        /**
         * @return FrameworkConfig
         */
        public function getConfig() {
            return $this->oConfig;
        }
    }
    
    interface DependentClassInterface {
        public function getData();
    }
    
    class DependentClass implements DependentClassInterface {
        public function getData() {
            return 'Dependency-Injection-Container-Exists';
        }
        
        static function __build() {
            return new DependentClass();
        }
    }

    class DependentClassNoBuild {
        public function getData() {
            return 'Dependency-Injection-Container-Exists';
        }
    }


    class FactoryMethodDependentClass {
        public function getData() {
            return 'Factory-Method-Exists';
        }

        /**
         * @return FactoryMethodDependentClass
         */
        public static function __build() {
            return new FactoryMethodDependentClass();
        }
    }
    
    trait MockEndPointDependencyContainer {

        public function __dependency()
        {
            return [
                'indexWithDependencyInjection' => [
                    DependentClass::class => function () {
                        return new DependentClass();
                    }
                ],
                'indexDependencyInsideDependency' => [
                    DependencyWithOtherGlobalDependency::class => function (FrameworkConfig $oConfig) {
                        return new DependencyWithOtherGlobalDependency($oConfig);
                    }
                ]
            ];
        }

    }
    
    class MockEndPoint
    {

        use MockEndPointDependencyContainer;

        /**
         * @EndPoint()
         */
        public function index()
        {
            return true;
        }

        /**
         * @EndPoint()
         * @param null $di
         * @param DependentClass $oDependentClass
         * @return bool
         */
        public function indexWithDependencyInjection(
            /* @noinspection PhpUnusedParameterInspection */
            $di = null, DependentClass $oDependentClass) {
            return $oDependentClass->getData();
        }

        /**
         * @EndPoint()
         * @param null $di
         * @param DependentClassInterface $oDependentClass
         * @return bool
         */
        public function indexWithDependencyInjectionAsInterface(
            /* @noinspection PhpUnusedParameterInspection */
            $di = null, DependentClassInterface $oDependentClass) {
            return $oDependentClass->getData();
        }

        /**
         * @EndPoint()
         * @param null $di
         * @param FactoryMethodDependentClass $oDependentClass
         * @return bool
         */
        public function indexWithFactoryMethodDI(
            /* @noinspection PhpUnusedParameterInspection */
            $di = null, FactoryMethodDependentClass $oDependentClass) {
            return $oDependentClass->getData();
        }

        /**
         * @EndPoint()
         * @param null $di
         * @param DependentClassNoBuild $oDependentClass
         * @return bool
         */
        public function indexWithoutFactoryMethodDI(
            /* @noinspection PhpUnusedParameterInspection */
            $di = null, DependentClassNoBuild $oDependentClass) {
            return $oDependentClass->getData();
        }

        /**
         * @EndPoint()
         * @param null $di
         * @param DependencyWithOtherGlobalDependency $oDependentClass
         * @return Config
         */
        public function indexDependencyInsideDependency(
            /* @noinspection PhpUnusedParameterInspection */
            $di = null, DependencyWithOtherGlobalDependency $oDependentClass) {
            return $oDependentClass->getConfig()->getDefaultEndPoint();
        }
        
        /**
         * @EndPoint()
         * @throws File\Permission\Exception
         */
        public function getFile()
        {
            $oFile = new File('/tmp/test');
            $oFile->save('test');
            return $oFile;
        }

        /**
         * @EndPoint(ContentType="application/json")
         * @param $param1
         * @return mixed
         */
        public function getWithParams($param1)
        {
            return $param1;
        }

        /**
         * @EndPoint(ContentType="application/json")
         * @param $param1
         * @return mixed
         */
        public function getWithNotRequiredParams(
            /* @noinspection PhpUnusedParameterInspection */
            $param1 = null)
        {
            return true;
        }

        /**
         * @EndPoint(ContentType="application/json")
         */
        public function error()
        {
            throw new \Exception('TestException');
        }

        /**
         * @EndPoint(ContentType="application/json")
         */
        public function error404()
        {
            throw new \Exception('TestException', 404);
        }

        /**
         * @EndPoint(ContentType="application/json")
         */
        public function errorUnAuthorized()
        {
            throw new \Exception('TestException', Status::UNAUTHORIZED);
        }

    }

}
