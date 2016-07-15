<?php
namespace Workshop\FrontEnd\Param {

    use AtomPie\Web\Connection\Http\Url\Param;
    use AtomPie\Web\Connection\Http\Url\Param\IConstrain;

    /**
     * Class Integer is a representation of request parameter with validation if it
     * can be cast to integer.
     *
     * If validation fails exception 'Expected integer value as param [%s]' is thrown.
     * <br />
     * Example:
     *
     * URL encoded: <pre class="code">"Id"=1</pre>
     * Empty parameter: <pre class="code">{"Id":1}</pre>
     */
    class IntegerNumber extends Param implements IConstrain
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
         * @throws Param\Constrain\Exception
         */
        public function validate()
        {
            if (!is_numeric($this->getValue()) || $this->isNull()) {
                throw new Param\Constrain\Exception(sprintf('Expected integer value as param [%s]', $this->getName()));
            }

            return true;
        }
    }

}
