<?php
namespace WorkshopTest\Resource\EndPoint {

    require_once __DIR__ . '/../Page/DefaultPage.php';
    require_once __DIR__ . '/../Page/NotPage.php';

    use AtomPie\AnnotationTag\SaveState;
    use Generi\Object;
    use AtomPie\Boundary\Core\IAmService;
    use AtomPie\AnnotationTag\Client;
    use AtomPie\AnnotationTag\EndPoint;
    use AtomPie\AnnotationTag\Documentation;
    use AtomPie\AnnotationTag\EndPointParam;
    use AtomPie\AnnotationTag\Header;
    use AtomPie\AnnotationTag\Template;
    use Workshop\FrontEnd\Param\IntegerNumber;
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
         * @EndPoint(ContentType="application/json")
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
         * @param IntegerNumber $Integer
         * @param IAmService $oService
         */
        public function notEndPoint(Param $Id = null, IntegerNumber $Integer, IAmService $oService = null)
        {
            
        }
    }

}
