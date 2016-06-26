<?php
namespace WorkshopTest\Resource\UseCase {

    use Generi\Object;
    use AtomPie\Core\Annotation\Tag\EndPoint;
    use AtomPie\Core\Annotation\Tag\Header;
    use AtomPie\Web\Boundary\IAmSession;
    use AtomPie\Web\CookieJar;
    use WorkshopTest\Resource\Param\MyParam1;
    use WorkshopTest\Resource\Param\MySession1;
    use WorkshopTest\Resource\Repo\DataRepository;

    class UseCase extends Object
    {

        public $aData;
        public $sSession = 'not-set';
        /**
         * @var CookieJar
         */
        public $oCookieJar;
        /**
         * @var IAmSession
         */
        public $oSession;

        /**
         * @var MyParam1
         */
        public $oParam;

        /**
         * @EndPoint()
         * @Header(ContentType="application/json")
         * @param DataRepository $oRepo
         * @return array
         */
        public function index(DataRepository $oRepo)
        {
            return $oRepo->loadData();
        }

        public function getData(DataRepository $oRepo)
        {
            $this->aData = $oRepo->loadData();
        }

        public function getSession(MySession1 $oSession = null)
        {
            $this->sSession = $oSession->getValue();
        }

        public function getCookieJar(CookieJar $oCookieJar)
        {
            $this->oCookieJar = $oCookieJar;
        }

        public function getSessionJar(IAmSession $oSessionJar)
        {
            $this->oSession = $oSessionJar;
        }

        /**
         * @param MyParam1 $AnnotatedParam
         */
        public function getAnnotatedParam(MyParam1 $AnnotatedParam)
        {
            $this->oParam = $AnnotatedParam;
        }

    }

}
