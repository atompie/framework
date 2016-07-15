<?php
namespace WorkshopTest\Resource\Component {

    use AtomPie\System\Application;
    use AtomPie\Core\Dispatch\DispatchManifest;
    use WorkshopTest\Resource\Service;
    use AtomPie\Web\Environment;
    use AtomPie\Gui\Component;
    use AtomPie\Html\ScriptsCollection;
    use AtomPie\Html\Tag\Head;
    use AtomPie\AnnotationTag\EndPoint;
    use AtomPie\Web\Connection\Http\Content;
    use AtomPie\Web\Connection\Http\Url\Param;
    use AtomPie\AnnotationTag\Template;

    /**
     * @Template(File="MockComponent7.mustache")
     * Class MockComponent7
     * @package WorkshopTest\Resource\Component
     */
    class MockComponent7 extends Component
    {

        public $ComponentParam;
        public $Param;
        public $Head;
        public $Environment;

        public function __create(
            /** @noinspection PhpUnusedParameterInspection */
            Component\ComponentParam $ComponentParam,
            Param $Param,
            Head $Head,
            ScriptsCollection $ScriptsCollection,
            Environment $Environment,
            Application $Application,
            DispatchManifest $DispatchManifest,
            Service $Service
        ) {
            $Environment->getResponse()->setContent(new Content('Test'));
        }

        public function __factory(
            /** @noinspection PhpUnusedParameterInspection */
            Component $Component,
            Component\ComponentParam $ComponentParam,
            Param $Param,
            Head $Head,
            ScriptsCollection $ScriptsCollection,
            Environment $Environment,
            Application $Application,
            DispatchManifest $DispatchManifest,
            Service $Service
        ) {
            $this->ComponentParam = $ComponentParam;
            $this->Param = $Param;
            $this->Head = $Head;
            $this->Environment = $Environment;
        }

        public function __process(
            Component $Component,
            Component\ComponentParam $ComponentParam,
            Param $Param,
            Head $Head,
            ScriptsCollection $ScriptsCollection,
            Environment $Environment,
            Application $Application,
            DispatchManifest $DispatchManifest,
            Service $Service
        ) {

        }

        public function eventEvent()
        {

        }

        /**
         * @EndPoint()
         * @return MockComponent7
         */
        public static function EndPoint()
        {
            return new self();
        }
    }

}

