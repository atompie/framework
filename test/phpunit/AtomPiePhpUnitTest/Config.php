<?php
namespace AtomPiePhpUnitTest {

    use AtomPie\Core\FrameworkConfig;
    use AtomPie\System\Router;
    use AtomPie\System\Environment\EnvVariable;
    use AtomPie\Web\Environment;

    class Config
    {

        public static function get()
        {
            return new FrameworkConfig(
                Environment::getInstance(),
                new Router(__DIR__.'/../AtomPieTestAssets/Routing/Routing.php'),
                new ApplicationConfigDefinition(EnvVariable::getInstance()),
                __DIR__ . '/../',
                __DIR__ . '/../../AtomPieTestAssets/Resource/Theme',
                [
                    "\\AtomPieTestAssets\\Resource\\Mock"
                ],
                [
                    "\\AtomPieTestAssets\\Resource\\Mock"
                ]

            );
        }

    }

}
