<?php
namespace WorkshopTest\Resource\Page {

    use AtomPie\Gui\Page;
    use AtomPie\AnnotationTag\Template;
    use WorkshopTest\Resource\Component\MockComponent0;

    /**
     * @property string EventFlag
     * @property MockComponent0 Inner
     * @Template(File="Default/DefaultPage.mustache")
     */
    class DefaultPage extends Page
    {

        public function __create()
        {
            $this->EventFlag = 'No';
            $this->Inner = new MockComponent0('Inner');
        }

        public function eventEvent()
        {
            $this->EventFlag = 'Yes';
        }

        public function __toJson()
        {
            return true;
        }

        public function getEventFlag()
        {
            return $this->EventFlag;
        }

    }

}
