<?php
namespace AtomPiePhpUnitTest\Core\Mock {

    use Generi\Boundary\ICanBeIdentified;
    use Generi\Type;

    class MockComponent2 implements ICanBeIdentified
    {

        /**
         * @return \Generi\Type
         */
        public function getType()
        {
            return Type::getTypeOf(self::class);
        }

        /**
         * @return string
         */
        public function getName()
        {
            return '2';
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
