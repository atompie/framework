<?php
namespace WorkshopTest\Resource\EndPoint {

    require_once __DIR__ . '/../Page/DefaultPage.php';
    require_once __DIR__ . '/../Page/NotPage.php';

    use Generi\Object;
    use AtomPie\Core\Annotation\Tag\EndPoint;
    use AtomPie\Gui\Component\Annotation\Tag\Template;
    use AtomPie\Web\Connection\Http\Url\Param;
    use WorkshopTest\Resource\Page\MockPage;

    /**
     * @Template(File="Default/MockInterfaceEndPoint.mustache")
     */
    class MockInterfaceEndPoint extends Object
    {

        public static $staticCall = false;
        public static $staticCallParam;

        /**
         * @EndPoint()
         * @return MockPage
         */
        public function index()
        {
            $oPage = new MockPage();
            return $oPage;
        }

        /**
         * @EndPoint()
         * @param Param $param
         * @return bool
         */
        public static function staticCall(Param $param = null)
        {
            self::$staticCall = true;
            if (!$param->isNull()) {
                self::$staticCallParam = $param->getValue();
            }
            return true;
        }

        /**
         * @EndPoint(ContentType="application/json")
         * @return bool
         */
        public static function staticJsonCall()
        {
            return true;
        }

    }

}
