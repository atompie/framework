<?php
namespace AtomPie\Gui\Component {

    use AtomPie\Boundary\Core\Dispatch\IAmDispatchManifest;
    use AtomPie\Boundary\Core\IAmService;
    use AtomPie\Boundary\Gui\Component\IProvideEventUrl;
    use AtomPie\DependencyInjection\Boundary\IAmDependencyMetaData;
    use AtomPie\DependencyInjection\Dependency;
    use AtomPie\DependencyInjection\DependencyContainer;
    use AtomPie\Boundary\Gui\Component\IAmComponentParam;
    use AtomPie\DependencyInjection\DependencyInvokeMetaData;
    use AtomPie\Core\Service\RequestParamService;
    use AtomPie\Web\Boundary\IPersistParamState;
    use AtomPie\Web\Boundary\IAmEnvironment;
    use AtomPie\Html\HtmlHeader;
    use AtomPie\Html\PageBottom;
    use AtomPie\Html\ScriptsCollection;
    use AtomPie\Html\Tag\Head;
    use AtomPie\Web\Connection\Http\UploadFile;
    use AtomPie\Web\Connection\Http\Url\Param;

    class ComponentDependencyContainer extends DependencyContainer
    {

        const EVENT_CLOSURE_ID = '@EventClosure';

        /**
         * DependencyContainer constructor.
         * @param IAmEnvironment $oEnvironment
         * @param IAmDispatchManifest $oDispatchManifest
         * @param IPersistParamState $oStateSaver
         * @throws \AtomPie\DependencyInjection\Exception
         */
        public function __construct(
            IAmEnvironment $oEnvironment,
            IAmDispatchManifest $oDispatchManifest,
            IPersistParamState $oStateSaver
        ) {

            $oSession = $oEnvironment->getSession();
            $oRequest = $oEnvironment->getRequest();
            $sParamNamespace = $oDispatchManifest->getEndPoint()->__toString();

            /////////////////////////////////////////////
            // Any function.
            // This means events handled by closures.

            $this->forFunction(self::EVENT_CLOSURE_ID)->setDependency(
                [
                    IProvideEventUrl::class => function () use ($oDispatchManifest, $oRequest) {
                        return new EventUrlProvider($oDispatchManifest, $oRequest->getAllParams()->getAll());
                    },
                    UploadFile::class => function (IAmDependencyMetaData $oMeta) {
                        return new UploadFile($oMeta->getParamMetaData()->name);
                    },
                    Dependency::TYPE_LESS => function (IAmDependencyMetaData $oMeta) use (
                        $oEnvironment,
                        $sParamNamespace
                    ) {
                        return RequestParamService::factoryTypeLessRequestParam(
                            $oMeta,
                            $oEnvironment,
                            $sParamNamespace  // As namespace for StatePersister
                        );
                    },
                    // ComponentParam. Not accessible when static EndPoint is called
                    IAmComponentParam::class =>
                        function (IAmDependencyMetaData $oMeta)
                        use ($oRequest, $oStateSaver) {
                            return ComponentDependencyFactory::factoryComponentRequestParam(
                                $oMeta,
                                $oRequest,
                                $oStateSaver
                            );
                        },
                    Param::class => function (IAmDependencyMetaData $oMeta) use (
                        $oRequest,
                        $oSession,
                        $sParamNamespace
                    ) {
                        return RequestParamService::factoryClosureParameter(
                            $oMeta,
                            $oSession,
                            $oRequest,
                            $sParamNamespace
                        );
                    },
                    Head::class => function () {
                        return HtmlHeader::getInstance()->getHeadTag();
                    },
                    ScriptsCollection::class => function () {
                        return PageBottom::getInstance()->getScriptCollection();
                    },
                    IAmEnvironment::class => function () use ($oEnvironment) {
                        return $oEnvironment;
                    },
                    IAmService::class => function (IAmDependencyMetaData $oMeta) {
                        $sClassName = $oMeta->getParamMetaData()->getClass()->getName();
                        return new $sClassName();
                    },
                    Part::class => function (IAmDependencyMetaData $oMeta) {
                        return $oMeta->getObject();
                    }
                ]
            );

            /////////////////////////////////////////////
            // All method in Part class
            // This means __create, __factory, __process
            // and any Event methods.

            $this->forAnyMethodInClass(Part::class)->setDependency(
                [
                    IProvideEventUrl::class => function () use ($oDispatchManifest, $oRequest) {
                        return new EventUrlProvider($oDispatchManifest, $oRequest->getAllParams()->getAll());
                    },
                    // TODO it should be called Parameter::Type_Less
                    Dependency::TYPE_LESS => function (DependencyInvokeMetaData $oMeta) use (
                        $oEnvironment,
                        $sParamNamespace
                    ) {
                        return RequestParamService::factoryTypeLessRequestParam(
                            $oMeta,
                            $oEnvironment,
                            $sParamNamespace  // For StaterPersister namespace
                        );
                    },
                    // ComponentParam. Not accessible when static EndPoint is called
                    IAmComponentParam::class =>
                        function (IAmDependencyMetaData $oMeta)
                        use ($oRequest, $oStateSaver) {
                            return ComponentDependencyFactory::factoryComponentRequestParam(
                                $oMeta,
                                $oRequest,
                                $oStateSaver
                            );
                        },
                    Param::class => function (IAmDependencyMetaData $oMeta) use (
                        $oRequest,
                        $oSession,
                        $sParamNamespace
                    ) {
                        return RequestParamService::factoryRequestParameter(
                            $oMeta,
                            $oSession,
                            $oRequest,
                            $sParamNamespace // Namespace
                        );
                    },
                    Head::class => function () {
                        return HtmlHeader::getInstance()->getHeadTag();
                    },
                    ScriptsCollection::class => function () {
                        return PageBottom::getInstance()->getScriptCollection();
                    },
                    IAmEnvironment::class => function () use ($oEnvironment) {
                        return $oEnvironment;
                    },
                    IAmService::class => function (IAmDependencyMetaData $oMeta) {
                        $sClassName = $oMeta->getParamMetaData()->getClass()->getName();
                        return new $sClassName();
                    },
                    Part::class => function (IAmDependencyMetaData $oMeta) {
                        return $oMeta->getObject();
                    }
                ]
            );

        }

    }

}
