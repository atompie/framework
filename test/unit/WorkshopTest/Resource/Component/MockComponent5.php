<?php
namespace WorkshopTest\Resource\Component {

    use AtomPie\Core\Annotation\Tag\EndPointParam;
    use AtomPie\Gui\Component;
    use AtomPie\Gui\Component\Annotation\Tag\Template;
    use AtomPie\Core\Annotation\Tag\EndPoint;
    use AtomPie\Web\Connection\Http\Url\Param;

    /**
     * Class MockComponent5
     * @package WorkshopTest\Resource\Component
     * @Template(File="WorkshopTest/Resource/Theme/Default/MockComponent5.mustache")
     */
    class MockComponent5 extends Component
    {

        public function __create()
        {
            $oObject = new \stdClass();
            $oObject->A = 'a';
            $this->Mock1 = new MockComponent0();
            $this->Mock2 = new MockComponent1();
            $this->Mock2->Value = 'value';
            $this->Mock2->Object = $oObject;
            $this->Mock2->Inner = new MockComponent0();
            $this->Mock3Array = array();
            $this->Mock3Array[] = 'Value';
            $this->Mock3Array[] = new MockComponent0();
            $this->Mock3Array[] = new MockComponent0();
            $this->Mock3Array[] = new MockComponent2();
            $this->Mock3Array[3] = new MockComponent3();
            $this->Mock3Array[3]->x = 'triggerEventHandlerWithDependencyInjection';
        }

        /**
         * @EndPoint(ContentType="application/json")
         * @EndPointParam(Name="oParam",Description="Param description")
         * @param Param $oParam
         */
        public function endPoint(Param $oParam)
        {

        }
    }

}
