<?php
namespace AtomPie\Gui\Component\Template {

    use AtomPie\Gui\Component\Template;
    use AtomPie\Html\ScriptsCollection;
    use AtomPie\Html\Tag\Head;

    class Master extends Template
    {
        public function __construct($sContent, Head $oHeader, ScriptsCollection $oScriptsCollection)
        {
            parent::__construct(__DIR__ . '/Master.html', $sContent, 'utf-8', $oHeader, $oScriptsCollection);
        }
    }
}
