<?php
namespace Workshop\FrontEnd\Param {

    use AtomPie\Web\Connection\Http\Url\Param;
    use AtomPie\Web\Connection\Http\Url\Param\Constrain;

    /**
     * Class Object is a representation of request parameter. Object is validated if it
     * defines ObjectType and a set of its properties.
     *
     * If validation fails exception 'Expected set of values as param [%s]' is thrown.
     * <br/>
     * Example of object Person with its properties
     *
     * URL encoded: <pre class="code">Person[Name]=John&Person[Surname]=Doe</pre>
     * JSON: <pre class="code">{ "Person": { "Name":"John", "Surname":"Doe" } }</pre>
     */
    class Model extends Param implements Param\IConstrain
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
            if (!$this->isArray()) {
                throw new Param\Constrain\Exception(sprintf('Expected set of values as param [%s]', $this->getName()));
            }

            return true;
        }
    }

}
