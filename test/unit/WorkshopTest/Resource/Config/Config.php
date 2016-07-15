<?php
namespace WorkshopTest\Resource\Config {

    use AtomPie\Core\FrameworkConfig;
    use AtomPie\File\FileProcessorProvider;
    use AtomPie\Gui\Component\ComponentProcessorProvider;
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
            $sViewFolder = __DIR__ . '/../../../WorkshopTest/Resource/Theme';
            
            return new FrameworkConfig(
                'Main',
                new EndPointConfig(
                    new Namespaces([
                        "\\WorkshopTest\\Resource\\EndPoint",
                        "\\WorkshopTest\\Resource\\Component",
                        'WorkshopTest\Resource\Operation',
                    ]),
                    new Namespaces([
                        'Test1\\Class4',
                        "\\WorkshopTest\\Resource\\EndPoint\\DefaultController"
                    ])
                ),
                new ApplicationConfigSwitcher($oEnvironment->getEnv()),
                $oEnvironment
                , []
                , []
                , [
                    new FileProcessorProvider(),
                    new ComponentProcessorProvider($sViewFolder, $oEnvironment)
                ],
                null,
                new EventConfig(
                    new Namespaces([
                        "\\WorkshopTest\\Resource\\Component",
                        "\\WorkshopTest\\Resource",
                        "\\WorkshopTest\\Resource\\EndPoint",
                    ])
                )
            );
        }

    }

}
