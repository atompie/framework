<?php
namespace WorkshopTest\Resource\Component {

    use AtomPie\Gui\Component;
    use AtomPie\AnnotationTag\Client;
    use AtomPie\AnnotationTag\EndPoint;
    use AtomPie\AnnotationTag\Template;

    /**
     * Class MockComponent1
     * @package WorkshopTest\Resource\Component
     * @Template(File="WorkshopTest/Resource/Theme/Default/MockComponent1.mustache")
     */
    class MockComponent8
    {

        /**
         * @Client(Accept="application/json")
         * @EndPoint(ContentType="application/json")
         */
        public function requireJsonContent()
        {
            return 'requireJsonContent';
        }

        /**
         * @EndPoint()
         * @param $test
         * @return string
         */
        public function requireJsonParam($test)
        {
            return 'requireJsonParam';
        }

        /**
         * @EndPoint()
         * @param $test
         * @return string
         */
        public function requireXmlParam($test)
        {
            return 'requireXmlParam';
        }

        /**
         * @EndPoint()
         * @Client(ContentType="application/json")
         */
        public function requireJsonContentType()
        {

        }

        /**
         * @EndPoint()
         * @Client(ContentType="application/xml",Method="PUT")
         */
        public function requireJsonPutContentType()
        {

        }

        /**
         * @EndPoint()
         * @Client(ContentType="application/xml",Method="POST",Type="Cli")
         */
        public function requireJsonAjaxContentType()
        {

        }
    }
}
