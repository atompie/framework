<?php
namespace AtomPiePhpUnitTest {

    use AtomPie\Core\FrameworkConfig;
    use AtomPie\File\FileProcessorProvider;
    use AtomPie\Gui\Component\ComponentProcessorProvider;
    use AtomPie\System\EndPointConfig;
    use AtomPie\System\EventConfig;
    use AtomPie\System\Namespaces;
    use AtomPie\System\Environment\EnvVariable;
    use AtomPie\Web\Environment;

    class Config
    {

        public static function get()
        {
            $oEnvironment = Environment::getInstance();
            $sViewFolder =  __DIR__ . '/../../AtomPieTestAssets/Resource/Theme';
            
            return new FrameworkConfig(
                'Main'
                , new EndPointConfig(
                    new Namespaces([
                        "\\AtomPieTestAssets\\Resource\\Mock"
                    ])
                )
                , new ApplicationConfigSwitcher(EnvVariable::getInstance())
                , Environment::getInstance()
                , []
                , []
                , [
                    new FileProcessorProvider(),
                    new ComponentProcessorProvider($sViewFolder, $oEnvironment)
                ] // Content processors
                , null
                , new EventConfig(
                    new Namespaces([
                        "\\AtomPieTestAssets\\Resource\\Mock"
                    ])
                )
            );
        }

    }

}
