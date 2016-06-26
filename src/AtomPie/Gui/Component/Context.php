<?php
namespace AtomPie\Gui\Component {

    use AtomPie\Boundary\Gui\Component\IHaveContext;
    use AtomPie\Gui\Component;

    class Context implements IHaveContext
    {

        const ROOT_NAMESPACE = '';

        ///////////////////////////
        // \IHaveContext

        /**
         * @return Component\NamespaceValue
         */
        public function getNamespace()
        {
            return new Component\NamespaceValue(self::ROOT_NAMESPACE, $this->getName());
        }

        /**
         * @return string
         */
        public function getName()
        {
            return self::ROOT_NAMESPACE;
        }

        /**
         * @return boolean
         */
        public function hasName()
        {
            return true;
        }
    }
}
