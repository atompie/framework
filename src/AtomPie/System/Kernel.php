<?php
namespace AtomPie\System {

    use Composer\Autoload\ClassLoader;
    use AtomPie\EventBus\EventHandler;
    use AtomPie\Boundary\Core\IAmFrameworkConfig;
    use AtomPie\Core\Dispatch\DispatchManifest;
    use AtomPie\System\Error\DefaultErrorHandler;
    use AtomPie\Boundary\System\IHandleException;
    use AtomPie\Boundary\System\IRunAfterMiddleware;
    use AtomPie\Boundary\System\IRunBeforeMiddleware;
    use AtomPie\Web\Boundary\IAmEnvironment;
    use AtomPie\Web\Boundary\IChangeResponse;
    use AtomPie\Web\Connection\Http\Header\Status;
    use AtomPie\Web\Connection\Http\Request;
    use AtomPie\Web\Connection\Http\Response;

    /**
     * Responsible for:
     *  - running apps within middleware
     *  - handling errors
     *
     * Class Kernel
     * @package Workshop
     */
    class Kernel
    {

        use EventHandler;

        const EVENT_APPLICATION_BOOT = '@ApplicationBoot';
        const EVENT_KERNEL_BOOT = '@KernelBoot';

        /**
         * @var array
         */
        private $aMiddleWares;

        /**
         * @var IAmFrameworkConfig
         */
        private $oConfig;

        /**
         * @var IAmEnvironment
         */
        private $oEnvironment;

        /**
         * @var ClassLoader
         */
        private $oClassLoader;

        /**
         * @var Bootstrap
         */
        private $oBootstrap;

        public function __construct(
            IAmEnvironment $oEnvironment
            , ClassLoader $oClassLoader = null
        ) {
            $this->oEnvironment = $oEnvironment;
            $this->oClassLoader = $oClassLoader;
        }
        
        public function boot(
            IAmFrameworkConfig $oConfig
        ) {

            $oCustomDependencyContainer = null;

            $this->aMiddleWares = $oConfig->getMiddleware();
            $this->oConfig = $oConfig;
            
            $this->oBootstrap = new Bootstrap($oConfig, $this->oEnvironment);

            $oResponse = $this->oEnvironment->getResponse();

            $this->triggerEvent(self::EVENT_KERNEL_BOOT, $this);

            try {

                $this->runBefore(
                    $this->oEnvironment->getRequest(),
                    $oResponse
                );

                $oDispatchManifest = DispatchManifest::factory(
                    $this->oEnvironment->getRequest(),
                    $this->oConfig,
                    $this->oConfig->getDefaultEndPoint()
                );

                $oApplication = $this->oBootstrap->initApplication(
                    $oDispatchManifest,
                    $oCustomDependencyContainer,
                    $oConfig->getContentProcessors()
                );

                $this->triggerEvent(self::EVENT_APPLICATION_BOOT, $oApplication);

                $oResponse = $oApplication->run($this->oConfig);

            } catch (\Exception $oException) {

                $oErrorRenderer = $oConfig->getErrorHandler();
                
                if ($oErrorRenderer == null) {
                    // Default value
                    $oErrorRenderer = new DefaultErrorHandler(true);
                }

                $oResponse = $this->handleError(
                    $oException,
                    $oErrorRenderer
                );

            } finally {
                $this->runAfter($oResponse);
            }

            return $oResponse;
        }

        /**
         * @param \Exception $oException
         * @param \AtomPie\Boundary\System\IHandleException $oErrorRenderer
         * @return Response
         */
        private function handleError(\Exception $oException, IHandleException $oErrorRenderer)
        {

            $oResponse = $this->oEnvironment->getResponse();

            $iStatusCode = $oException->getCode();

            if ($iStatusCode == Status::UNAUTHORIZED) {
                $oResponse->addHeader('WWW-Authenticate', 'Basic realm="Secured Area"');
                $oResponse->setStatus(new Status(Status::UNAUTHORIZED));
            } else {
                if ($iStatusCode !== 0 && Status::isValidStatusCode($iStatusCode)) {
                    $oResponse->setStatus(new Status($iStatusCode));
                } else {
                    $oResponse->setStatus(new Status(Status::INTERNAL_SERVER_ERROR));
                }
            }

            $oResponse
                ->getContent()
                ->setContent(
                    $oErrorRenderer->handleException(
                        $oException,
                        $oResponse->getContent()->getContentType()
                    )
                );

            return $oResponse;
        }

        /**
         * @param $oRequest
         * @param $oResponse
         * @return mixed
         * @throws Exception
         */
        private function runBefore($oRequest, $oResponse)
        {

            if (null !== $this->aMiddleWares && !empty($this->aMiddleWares)) {

                foreach ($this->aMiddleWares as $oMiddleWare) {
                    if ($oMiddleWare instanceof IRunBeforeMiddleware) {
                        $oMiddleWare->before($oRequest, $oResponse);
                    }
                }

            }
        }

        /**
         * @param $oResponse
         * @return IChangeResponse
         * @throws Exception
         */
        private function runAfter($oResponse)
        {
            if (null !== $this->aMiddleWares && !empty($this->aMiddleWares)) {
                foreach ($this->aMiddleWares as $oMiddleWare) {
                    if ($oMiddleWare instanceof IRunAfterMiddleware) {
                        $oMiddleWare->after($oResponse);
                    }
                }
            }
        }
    }

}
