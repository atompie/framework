<?php
namespace WorkshopTest\Resource\EndPoint {

    require_once __DIR__ . '/../Page/DefaultPage.php';
    require_once __DIR__ . '/../Page/NotPage.php';

    use Generi\Object;
    use AtomPie\Boundary\Core\IAmService;
    use AtomPie\Core\Annotation\Tag\Client;
    use AtomPie\Core\Annotation\Tag\EndPoint;
    use AtomPie\Core\Annotation\Tag\Documentation;
    use AtomPie\Core\Annotation\Tag\EndPointParam;
    use AtomPie\Core\Annotation\Tag\Header;
    use AtomPie\Gui\Component\Annotation\Tag\Template;
    use AtomPie\Core\Annotation\Tag\SaveState;
    use Workshop\FrontEnd\Param\Integer;
    use AtomPie\Web\Connection\Http\Url\Param;
    use WorkshopTest\Resource\Component\Yes;
    use WorkshopTest\Resource\Page\DefaultPage;
    use WorkshopTest\Resource\Page\NotPage;

    /**
     * Class DefaultController
     * Holds all endpoints
     * @Template(File="DefaultController.mustache")
     */
    class DefaultController extends Object
    {

        /**
         * @EndPoint()
         * @return DefaultPage
         */
        public function index()
        {
            $oPage = new DefaultPage();
            $oPage->Inner->handleEvent('OutsideEvent', function () use ($oPage) {
                $oPage->Inner->EventFlag = 'OutsideYes';
            });

            return $oPage;
        }

        /**
         * @EndPoint()
         * @SaveState(Param="Name")
         * @param $Name
         * @return mixed
         */
        public function simpleParam($Name)
        {
            return $Name;
        }

        /**
         * @EndPoint()
         * @return string
         */
        public function yes()
        {
            return new Yes('Test');
        }

        /**
         * @EndPoint
         * @return DefaultPage
         */
        public function annotatedMethod()
        {
            return new DefaultPage();
        }

        /**
         * Return object that is not Page type.
         *
         * @return NotPage
         * @EndPoint()
         */
        public function NotPage()
        {
            return new NotPage();
        }

        /**
         * @return DefaultPage
         * @Header(ContentType="application/json")
         * @EndPoint()
         */
        public function JsonContentType()
        {
            return new DefaultPage();
        }

        /**
         * @EndPoint()
         * @Header(ContentType="application/json")
         * @Client(Accept="application/json")
         */
        public function AcceptHeader()
        {

        }

        /**
         * @EndPoint
         * @return null
         */
        public function noPage()
        {
            return null;
        }

        /**
         *
         * This is documented
         *      endpoint
         *
         * {{ File }}
         *
         * sfsd
         *
         * @EndPoint()
         * @EndPointParam(Name="Id",Description="IdDesc")
         * @EndPointParam(Name="Integer",Description="IntDesc")
         * @EndPointParam(Name="oService",Description="ServiceDesc")
         * @Documentation(Name="File",File="WorkshopTest/Resource/EndPoint/DefaultController.php")
         *
         * @param Param $Id
         * @param Integer $Integer
         * @param IAmService $oService
         */
        public function notEndPoint(Param $Id = null, Integer $Integer, IAmService $oService = null)
        {
        }
    }

}
