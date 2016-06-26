<?php
namespace WorkshopTest\Resource\Component {

    use AtomPie\Gui\Component;

    class MultiLevel1 extends Component
    {
        public function __create()
        {
            $this->Property1 = 'Wlasciwosc1';
            $this->Property2 = new \stdClass();
            $this->Property2->Property1 = 'std11';
        }
    }

}
