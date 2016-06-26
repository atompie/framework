<?php
namespace AtomPie\Html\Tag {

    use AtomPie\Html;
    use Generi\Boundary;

    /**
     * Holds info on header title.
     *
     * @author risto
     */
    class Title implements Boundary\IStringable
    {
        /**
         * @var Html\ElementNode
         */
        private $oTitle;

        public function __construct($sTitle)
        {
            $this->oTitle = new Html\ElementNode('title');
            $this->oTitle->addChild(new Html\TextNode($sTitle));
        }

        /**
         * @return string
         */
        public function __toString()
        {
            return $this->oTitle->__toString();
        }

        /**
         * @return Html\ElementNode
         */
        public function getXhtmlNode()
        {
            return $this->oTitle;
        }

    }
}