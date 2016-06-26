<?php
namespace WorkshopTest\Resource\Config {

    use AtomPie\Core\FrameworkConfig;
    use AtomPie\System\Router;
    use AtomPie\Web\Environment;
    use AtomPiePhpUnitTest\ApplicationConfigDefinition;

    class TestConfig
    {

        public static function get()
        {
            $oEnvironment = Environment::getInstance();
            return new FrameworkConfig(
                $oEnvironment,
                new Router(__DIR__.'/../../Routing/Routing.php'),
                new ApplicationConfigDefinition($oEnvironment->getEnv()),
                __DIR__ . '/../../../../../test/unit/',
                __DIR__ . '/../../../../../test/unit/WorkshopTest/Resource/Theme',
                [
                    'WorkshopTest\\Resource\\EndPoint',
                    'WorkshopTest\\Resource\\UseCase'
                ],
                [
                    "\\WorkshopTest\\Resource\\Component"
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
