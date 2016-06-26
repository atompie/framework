<?php
namespace Workshop\FrontEnd\Param {

    use AtomPie\Web\Connection\Http\Url\Param;
    use AtomPie\Web\Connection\Http\Url\Param\Constrain;

    class Boolean extends Param implements Param\IConstrain
    {

        /**
         * This method can throw Exception. Value can be read from
         * $this->getValue(). Remember Value can be array or string.
         *
         * If you want to change response status from INTERNAL_SERVER_ERROR
         * @see \AtomPie\Web\Connection\Http\Status\Header pass code with
         * Exception.
         *
         * @return bool
         * @throws Constrain\Exception
         */
        public function validate()
        {
            if (!in_array($this->getValue(), ['0', '1'])) {
                throw new Param\Constrain\Exception(sprintf('Expected boolean value as param [%s]', $this->getName()));
            }

            return true;
        }

    }

}
