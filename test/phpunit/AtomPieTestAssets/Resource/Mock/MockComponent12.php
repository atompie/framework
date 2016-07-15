<?php
namespace AtomPieTestAssets\Resource\Mock {

    use AtomPie\Gui\Component;
    use AtomPie\AnnotationTag\EndPoint;

    /**
     * @property int Property1
     * @property int Property2
     *
     * @EndPoint(ContentType="application/xml")
     * This class will be sent to browser as ajax and
     * do not need @Template annotation
     * @package WorkshopTest\Resource\Component
     */
    class MockComponent12 extends Component
    {
        public function __create()
        {
            $this->Property1 = 1;
            $this->Property2 = 2;
        }
    }

}

