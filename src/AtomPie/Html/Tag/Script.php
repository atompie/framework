<?php
namespace AtomPie\Html\Tag {

    use AtomPie\Html\Attribute;
    use AtomPie\Html\ElementNode;
    use AtomPie\Html\TextNode;
    use AtomPie\Html\Boundary\IToStringWrap;

    class Script extends ElementNode implements IToStringWrap
    {

        private $onCondition;

        public function __construct($sScriptPath = null, $sContent = null)
        {
            parent::__construct('script');
            $this->addAttribute(new Attribute('type', 'text/javascript'));
            if (!is_null($sScriptPath)) {
                $this->addAttribute(new Attribute('src', $sScriptPath));
            }
            if (!is_null($sContent)) {
                $this->addChild(
                    new TextNode($sContent)
                );
            }
            $this->closeTag();
        }

        public function onCondition($onCondition)
        {
            $this->onCondition = $onCondition;
        }

        public function __wrapToString($sString)
        {
            if (is_null($this->onCondition)) {
                return $sString;
            }

            return '<!--[' . $this->onCondition . ']>' . $sString . '<![endif]-->';
        }
    }
}