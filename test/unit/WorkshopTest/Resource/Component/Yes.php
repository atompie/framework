<?php
namespace WorkshopTest\Resource\Component {

    use AtomPie\Gui\Component;
    use AtomPie\AnnotationTag\Template;

    /**
     * Class Yes
     * @property string Yes
     * @package WorkshopTest\Resource\Component
     * @Template(File="Default/Yes.mustache")
     */
    class Yes extends Component
    {

        public function __create()
        {
            $this->Yes = 'yes';
        }


        public function noEvent()
        {
            $this->Yes = 'no';
        }

    }

}
