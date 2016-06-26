<?php
namespace AtomPieTestAssets\Resource\Mock {

    use AtomPie\Gui\Component;
    use AtomPie\Core\Annotation\Tag\EndPoint;

    /**
     * @property int Property1
     * @property int Property2
     *
     * @EndPoint(ContentType="application/json")
     * This class will be sent do browser as ajax and
     * do not need @Template annotation
     * @package WorkshopTest\Resource\Component
     */
    class MockComponent11 extends Component
    {
        public function __create()
        {
            $this->Property1 = 1;
            $this->Property2 = 2;
        }
    }

}

