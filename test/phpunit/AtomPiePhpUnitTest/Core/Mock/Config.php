<?php
namespace AtomPiePhpUnitTest\Core\Mock {

    use AtomPie\Core\FrameworkConfig;
    use AtomPie\System\EndPointConfig;
    use AtomPie\System\EventConfig;
    use AtomPie\System\Namespaces;
    use AtomPie\Web\Environment;
    use AtomPiePhpUnitTest\ApplicationConfigSwitcher;

    class Config
    {

        public static function get()
        {
            $oEnvironment = Environment::getInstance();

            return new FrameworkConfig(
                'Main',
                new EndPointConfig(
                    new Namespaces([
                        "\\WorkshopTest\\Resource\\EndPoint",
                        "\\WorkshopTest\\Resource\\Component",
                        'WorkshopTest\Resource\Operation'
                    ]),
                    new Namespaces([
                        'Test1\\Class4',
                        "\\WorkshopTest\\Resource\\EndPoint\\DefaultController"
                    ])
                ),
                new ApplicationConfigSwitcher($oEnvironment->getEnv()),
                $oEnvironment,
                [],
                [],
                [],
                null,
                new EventConfig(
                    new Namespaces([
                        "\\WorkshopTest\\Resource\\Component",
                        "\\WorkshopTest\\Resource",
                        "\\WorkshopTest\\Resource\\EndPoint",
                        '\\AtomPiePhpUnitTest\\Core\\Mock'
                    ])
                )
            );
        }

    }

}
