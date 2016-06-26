<?php
namespace AtomPie\Html\Tag {

    use AtomPie\Html\Attribute;
    use AtomPie\Html\ElementNode;
    use AtomPie\Html\Boundary\IToStringWrap;

    class CssLink extends ElementNode implements IToStringWrap
    {

        private $sOnCondition;

        public function __construct($sCssPath, $sOnCondition = null)
        {
            parent::__construct('link');
            $this->addAttribute(new Attribute('href', $sCssPath));
            $this->addAttribute(new Attribute('rel', 'stylesheet'));
            $this->sOnCondition = $sOnCondition;
        }

        public function __wrapToString($sString)
        {
            if (is_null($this->sOnCondition)) {
                return $sString;
            }

            return '<!--[' . $this->sOnCondition . ']>' . $sString . '<![endif]-->';
        }
    }
}