<?php
namespace AtomPiePhpUnitTest\Core\Mock {

    use AtomPie\Core\FrameworkConfig;
    use AtomPie\System\Router;
    use AtomPie\Web\Environment;
    use AtomPiePhpUnitTest\ApplicationConfigDefinition;

    class Config
    {

        public static function get()
        {
            $oEnvironment = Environment::getInstance();
            
            return new FrameworkConfig(
                $oEnvironment,
                new Router(__DIR__.'/Routing.php'),
                new ApplicationConfigDefinition($oEnvironment->getEnv()),
                __DIR__ . '/../../../',
                __DIR__ . '/../../../WorkshopTest/Resource/Theme',
                [
                    "\\WorkshopTest\\Resource\\EndPoint",
                    "\\WorkshopTest\\Resource\\Component",
                    'WorkshopTest\Resource\Operation'
                ],
                [
                    "\\WorkshopTest\\Resource\\Component",
                    "\\WorkshopTest\\Resource",
                    "\\WorkshopTest\\Resource\\EndPoint",
                    '\\AtomPiePhpUnitTest\\Core\\Mock'
                ],
                [
                    'Test1\\Class4',
                    "\\WorkshopTest\\Resource\\EndPoint\\DefaultController"
                ],
                []
            );
        }

    }

}
