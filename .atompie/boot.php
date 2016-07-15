<?php

use AtomPie\Boundary\System\IAmEnvVariable;
use AtomPie\Config\ApplicationConfig;
use AtomPie\Core\Config\ConfigSwitcher;
use AtomPie\Core\FrameworkConfig;
use AtomPie\MiddleWare\ApiVersioning;
use AtomPie\System\Kernel;
use AtomPie\System\Namespaces;
use AtomPie\Web\Environment;

class Config extends ApplicationConfig
{
    public function __construct(IAmEnvVariable $oEnv)
    {
        $this->set('test', 1);
    }
}

return function (Environment $oEnvironment, Kernel $oKernel) {

    $oKernel->handleEvent(Kernel::EVENT_APPLICATION_BOOT, function () {
        // Add event handlers
    });

    $oEndPointsNamespaces = new Namespaces([
        'Example\EndPoint\v1', // default namespace for endpoints
        'Example\EndPoint' // fallback namespace for endpoints
    ]);

    return new FrameworkConfig(
        "Hello"
        , new \AtomPie\System\EndPointConfig($oEndPointsNamespaces)
        , new ConfigSwitcher(Config::class)
        , $oEnvironment
        , [] // Contract fillers
        , [ // Middleware
            new ApiVersioning(
                'Example\EndPoint'
                , $oEndPointsNamespaces
                , 'application/vnd.atompie+json'
            )
        ]
        , [] // Content processors
    );
};
