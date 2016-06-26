<?php
namespace AtomPie\Gui\Component {

    use AtomPie\Boundary\Gui\Component\IAmComponentParam;
    use AtomPie\Web\Connection\Http\Url\Param;

    class ComponentParam extends Param implements IAmComponentParam
    {

        private $sComponentContext;

        /**
         * @return string
         */
        public function getComponentContext()
        {
            return $this->sComponentContext;
        }

        /**
         * @param string $sComponentContext
         */
        public function setComponentContext($sComponentContext)
        {
            $this->sComponentContext = $sComponentContext;
        }

        /**
         * @return bool
         */
        public function hasComponentContext()
        {
            return isset($this->sComponentContext);
        }

        /**
         * @return string
         */
        public function __toString()
        {

            if ($this->hasComponentContext()) {
                $this->sName = $this->sName . '[' . $this->getComponentContext() . ']';
            }
            return parent::__toString();
        }

    }

}
