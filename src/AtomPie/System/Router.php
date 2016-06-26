<?php
namespace AtomPie\System {

    use AtomPie\Boundary\System\IAmRouter;

    class Router extends \TreeRoute\Router implements IAmRouter
    {
        /**
         * @var string
         */
        private $sRoutesPath;

        public function __construct($sRoutesPath)
        {
            if (!is_file($sRoutesPath)) {
                throw new Exception(sprintf('[%s] if not file.', $sRoutesPath));
            }

            $this->sRoutesPath = $sRoutesPath;
        }

        public function dispatch($sMethod, $sShortUrl)
        {
            /** @noinspection PhpIncludeInspection */
            $routeManifests = require $this->sRoutesPath;

            if (!$routeManifests instanceof \Closure) {
                throw new Exception('Route definition do not return closure.');
            }

            $routeManifests($this);

            return parent::dispatch($sMethod, $sShortUrl);
        }

    }

}
