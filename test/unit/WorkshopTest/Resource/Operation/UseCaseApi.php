<?php
namespace WorkshopTest\Resource\Operation {

    use Generi\Object;
    use AtomPie\Web\Environment;
    use AtomPie\Core\Annotation\Tag\EndPoint;
    use AtomPie\Core\Annotation\Tag\Header;
    use WorkshopTest\Resource\Repo\DataRepository;

    class UseCaseApi extends Object
    {

        /**
         * @return bool
         * @Header(ContentType="application/json")
         * @EndPoint()
         */
        public function getJson()
        {
            return true;
        }

        /**
         * @return bool
         * @Header(ContentType="application/xml")
         * @Header(ContentType="application/json")
         * @EndPoint()
         */
        public function twoContentTypes()
        {
            return true;
        }

        /**
         * @return bool
         * @Header(ContentType="application/xml")
         * @EndPoint()
         */
        public function getArrayAsXml()
        {
            return array('tag' => 'value', 'nested' => array('node' => 1));
        }

        /**
         * @return bool
         * @Header(ContentType="application/xml")
         * @EndPoint()
         */
        public function getObjectAsXml()
        {
            $oObject = new \stdClass();
            $oObject->tag = 'value';
            $oObject->nested = new \stdClass();
            $oObject->nested->node = 1;

            return $oObject;
        }

        /**
         * @param DataRepository $oRepo
         * @return array
         *
         * @Header(ContentType="application/json")
         * @EndPoint()
         */
        public function getDataFromRepo(DataRepository $oRepo)
        {
            return $oRepo->loadData();
        }

        /**
         * @param \AtomPie\Web\Environment $oEnv
         * @return string
         *
         * @Header(ContentType="application/json")
         * @EndPoint()
         */
        public function getDataFromSession(Environment $oEnv)
        {
            $sSessionValue = $oEnv->getSession()->get('MySession1');
            return $sSessionValue;
        }

    }

}
