<?php
namespace AtomPie\Html\Tag {

    use AtomPie\Html\Attribute;
    use AtomPie\Html\ElementNode;
    use AtomPie\Html\ScriptsCollection;

    class Head extends ElementNode
    {

        const EVENT_ON_BEFORE_RENDER = '\Application\Template\Head\Tag::onBeforeRender';

        private $aTags = array();
        private $aAddedCss = array();
        private $oTitle;
        private $sEncoding;
        /**
         * @var ScriptsCollection
         */
        private $oScriptsCollection;

        public function __construct()
        {
            parent::__construct('head');
            $this->oScriptsCollection = new ScriptsCollection();
        }

        protected function beforeToString()
        {

            if (isset($this->oTitle)) {
                $this->addChild($this->oTitle);
            }

            if (isset($this->sEncoding)) {
                $oEncoding = new ElementNode('meta');
                $oEncoding->addAttribute(new Attribute('charset', $this->sEncoding));

                $this->addChild($oEncoding);
            }

            foreach ($this->aTags as $oTag) {
                $this->addChild($oTag);
            }

            foreach ($this->oScriptsCollection->getScripts() as $sKeyPath => $oScript) {
                if (!$this->hasChild($sKeyPath)) {
                    $this->addChild($oScript, $sKeyPath);
                }
                $this->oScriptsCollection->removeScript($sKeyPath);
            }

            foreach ($this->aAddedCss as $sKeyPath => $oCss) {
                if (!$this->hasChild($sKeyPath)) {
                    $this->addChild($oCss, $sKeyPath);
                }
                unset($this->aAddedCss[$sKeyPath]);
            }

        }

        public function setEncoding($sEncoding)
        {
            $this->sEncoding = $sEncoding;
        }

        /**
         * @param \AtomPie\Html\Tag\Title $oTitle
         */
        public function setTitle(Title $oTitle)
        {
            $this->oTitle = $oTitle->getXhtmlNode();
        }

        /////////////////////////////
        // Script handling

        /**
         * @param string $sScriptPath
         * @return boolean
         */
        public function hasScript($sScriptPath)
        {
            return $this->oScriptsCollection->hasScript($sScriptPath);
        }

        /**
         * @param string $sScriptPath
         * @return ElementNode
         */
        public function getScript($sScriptPath)
        {
            return $this->oScriptsCollection->getScript($sScriptPath);
        }

        /**
         * @param string $sScriptPath
         * @param string $sContent
         * @param bool $bOnTop
         * @param null $onCondition
         * @return Script
         */
        public function addScript($sScriptPath = null, $sContent = null, $bOnTop = false, $onCondition = null)
        {
            return $this->oScriptsCollection->addScript($sScriptPath, $sContent, $bOnTop, $onCondition);
        }

        /**
         * @param string $sScriptPath
         * @param string $sContent
         * @param string $sContentKey
         */
        public function appendContentToScript($sScriptPath, $sContent, $sContentKey = null)
        {
            $this->oScriptsCollection->appendContentToScript($sScriptPath, $sContent, $sContentKey);
        }

        ///////////////////////////
        // View Port

        public function addViewPort($sContent)
        {
            $oMeta = new ElementNode('meta');
            $oMeta->addAttribute(new Attribute('name', 'viewport'));
            $oMeta->addAttribute(new Attribute('content', $sContent));

            $this->addTag($oMeta);
        }

        ///////////////////////////
        // Css

        /**
         * @param string $sCssPath
         * @param bool $bOnTop
         * @param null $sOnCondition
         */
        public function addCss($sCssPath, $bOnTop = false, $sOnCondition = null)
        {
            if ($bOnTop) {
                $aTop = array();
                $aTop[$sCssPath] = $this->getPrepareCss($sCssPath, $sOnCondition);
                $this->aAddedCss = $aTop + $this->aAddedCss;
            } else {
                $this->aAddedCss[$sCssPath] = $this->getPrepareCss($sCssPath, $sOnCondition);
            }
        }

        /**
         * @param $sCssPath
         */
        public function removeCss($sCssPath)
        {
            unset($this->aAddedCss[$sCssPath]);
        }

        /**
         * @param $sCssPath
         * @return CssLink | null
         */
        public function getCss($sCssPath)
        {
            if ($this->hasCss($sCssPath)) {
                return $this->aAddedCss[$sCssPath];
            }
            return null;
        }

        public function hasCss($sCssPath)
        {
            return isset($this->aAddedCss[$sCssPath]);
        }

        private function getPrepareCss($sCssPath, $sOnCondition = null)
        {
            return new CssLink($sCssPath, $sOnCondition);
        }

        ///////////////////////////
        // Tags

        /**
         * @param ElementNode $oTag
         */
        public function addTag(ElementNode $oTag)
        {
            $this->aTags[] = $oTag;
        }

    }

}