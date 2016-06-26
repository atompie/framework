<?php
namespace WorkshopTest\Resource\Page {

    use AtomPie\Gui\Component\Annotation\Tag\Template;
    use AtomPie\Gui\Page;
    use AtomPie\Html\ScriptsCollection;
    use AtomPie\Html\Tag\Head;
    use WorkshopTest\Resource\Component\MockComponent4;

    /**
     * @Template(File="Default/HeadBottomPage.mustache")
     * @property MockComponent4 Component
     */
    class HeadBottomPage extends Page
    {

        public function __create(Head $oHeadTag, ScriptsCollection $oScriptsCollection)
        {
            $oHeadTag->addScript('top.js');
            $oScriptsCollection->addScript('bottom.js');
        }

    }

}
