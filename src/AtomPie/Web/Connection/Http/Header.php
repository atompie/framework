<?php
namespace AtomPie\Web\Connection\Http {

    use AtomPie\Web\Boundary\IAmHttpHeader;

    /**
     * Responsibility: Holds information on header. It is usually a pair of key, value.
     * It is a base class for further extensions such as Web\Connection\Http\Status\Header
     * and \Web\Connection\Http\ContentType.
     */
    class Header extends ImmutableHeader implements IAmHttpHeader
    {

        /**
         * Sets value of a header.
         *
         * @param $sValue
         */
        public final function setValue($sValue)
        {
            $this->sValue = $sValue;
        }

    }
}