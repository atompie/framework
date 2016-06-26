<?php
namespace AtomPie\Gui {

    use AtomPie\Gui\Component\Context;
    use AtomPie\Gui\Component\Part;
    use AtomPie\Html\HtmlHeader;
    use AtomPie\Html\PageBottom;

    /**
     * Common methods for FrontEnd events.
     *
     * Available annotations for controller actions:
     */
    abstract class Page extends Part
    {

        final public function __construct()
        {
            parent::__construct(Part::TOP_COMPONENT_NAME, new Context());
        }

        /**
         * @return HtmlHeader
         */
        public function getHeader()
        {
            return HtmlHeader::getInstance();
        }

        /**
         * @return PageBottom
         */
        public function getPageBottom()
        {
            return PageBottom::getInstance();
        }

    }

}