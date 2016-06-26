<?php
namespace WorkshopTest\Resource\Page {

    use AtomPie\Gui\Component\Annotation\Tag\Template;
    use AtomPie\Gui\Page;
    use WorkshopTest\Resource\Component\MockComponent4;

    /**
     * @Template(File="Default/MockPage.mustache")
     * @property MockComponent4 Component
     */
    class MockPage extends Page
    {

        public function __create()
        {
            $this->Component = new MockComponent4('Name4');
        }

    }

}
