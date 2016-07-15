<?php
namespace AtomPie\System\DependencyContainer {

    use AtomPie\Boundary\Core\Dispatch\IAmDispatchManifest;
    use AtomPie\Boundary\Core\Dispatch\IProvideUrl;
    use AtomPie\Boundary\Config\IAmApplicationConfig;
    use AtomPie\Boundary\Core\IAmService;
    use AtomPie\Boundary\Core\IAmFrameworkConfig;
    use AtomPie\DependencyInjection\Boundary\IAmDependencyMetaData;
    use AtomPie\DependencyInjection\Dependency;
    use AtomPie\DependencyInjection\DependencyContainer;
    use AtomPie\Core\Service\RequestParamService;
    use AtomPie\Web\Boundary\IAmEnvironment;
    use AtomPie\Web\Boundary\IAmRequest;
    use AtomPie\Web\Boundary\IAmSession;
    use AtomPie\Web\Boundary\IUploadFile;
    use AtomPie\Web\Connection\Http\Request;
    use AtomPie\Web\Connection\Http\Response;
    use AtomPie\Web\Connection\Http\UploadFile;
    use AtomPie\Web\Connection\Http\Url\Param;

    /**
     * Class EndPointDependencyContainer
     * @package AtomPie\System\DependencyContainer
     */
    class EndPointDependencyContainer extends DependencyContainer
    {

        /**
         * DependencyContainer constructor.
         * @param \AtomPie\Web\Boundary\IAmEnvironment $oEnvironment
         * @param IAmFrameworkConfig $oConfig
         * @param IAmDispatchManifest $oDispatchManifest
         * @param IProvideUrl $oThisEndPointUrl
         * @throws \AtomPie\DependencyInjection\Exception
         * @internal
         */
        public function __construct(
            IAmEnvironment $oEnvironment,
            IAmFrameworkConfig $oConfig,
            IAmDispatchManifest $oDispatchManifest,
            IProvideUrl $oThisEndPointUrl
        ) {

            $sParamNamespace = $oDispatchManifest->getEndPoint()->__toString();

            $this->forAnyClass()->setDependency(
                [
                    IProvideUrl::class => function () use ($oThisEndPointUrl) {
                        return $oThisEndPointUrl;
                    },
                    IUploadFile::class => function (IAmDependencyMetaData $oMeta) {
                        return new UploadFile($oMeta->getParamMetaData()->name);
                    },
                    Dependency::TYPE_LESS => function (IAmDependencyMetaData $oMeta)
                    use ($oEnvironment, $sParamNamespace) {
                        return RequestParamService::factoryTypeLessRequestParam(
                            $oMeta,
                            $oEnvironment,
                            $sParamNamespace  // Namespace - persister
                        );
                    },
//	                IAmRequestParam::class => function(DependencyInvokeMetaData $oMeta) use ($oEnvironment) { return DependencyFactory::factoryRequestParameter($oMeta, $oEnvironment, Param::class); },
                    Param::class => function (IAmDependencyMetaData $oMeta)
                    use ($oEnvironment, $sParamNamespace) {
                        return RequestParamService::factoryRequestParameter(
                            $oMeta,
                            $oEnvironment->getSession(),
                            $oEnvironment->getRequest(),
                            $sParamNamespace // Namespace
                        );
                    },
                    IAmService::class => function (IAmDependencyMetaData $oMeta) {
                        $sClassName = $oMeta->getParamMetaData()->getClass()->getName();
                        return new $sClassName();
                    },
                    IAmFrameworkConfig::class => function () use ($oConfig) {
                        return $oConfig;
                    },
                    IAmApplicationConfig::class => function () use ($oConfig) {
                        return $oConfig->getAppConfig();
                    },
                    IAmRequest::class => function () use ($oEnvironment) {
                        return $oEnvironment->getRequest();
                    },
                    Response::class => function () use ($oEnvironment) {
                        return $oEnvironment->getResponse();
                    },
                    IAmSession::class => function () use ($oEnvironment) {
                        return $oEnvironment->getSession();
                    },
                    IAmEnvironment::class => function () use ($oEnvironment) {
                        return $oEnvironment;
                    }
                ]
            );

            return $this;
        }

    }

}
