<?php
namespace WorkshopTest\Resource\Component {

    use AtomPie\Gui\Component;

    /**
     * @property string Object1
     * @property MultiLevel1 Object2
     * @property array Object3
     */
    class MultiLevel extends Component
    {

        public function __create()
        {
            $this->Object1 = 'Obiekt1';
            $this->Object2 = new MultiLevel1();
            $this->Object3 = array(
                'String1',
                new MultiLevel1()
            );
        }

    }

}
