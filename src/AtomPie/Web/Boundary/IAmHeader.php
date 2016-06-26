<?php
namespace AtomPie\Web\Boundary;

interface IAmHeader
{

    /**
     * Returns a name of a header, e.g. Location, Date, Accept, etc.
     * See http://en.wikipedia.org/wiki/List_of_HTTP_header_fields for header filed names.
     *
     * @return string $sName
     */
    public function getName();

    /**
     * Returns value of a header.
     *
     * @return string $sValue
     */
    public function getValue();

    /**
     * Returns header as in request or response.
     *
     * @return string
     */
    public function __toString();

    /**
     * Send a raw HTTP header
     * @link http://www.php.net/manual/en/function.header.php
     * @param string string <p>
     * The header string.
     * </p>
     * <p>
     * There are two special-case header calls. The first is a header
     * that starts with the string "HTTP/" (case is not
     * significant), which will be used to figure out the HTTP status
     * code to send. For example, if you have configured Apache to
     * use a PHP script to handle requests for missing files (using
     * the ErrorDocument directive), you may want to
     * make sure that your script generates the proper status code.
     * </p>
     * <p>
     * ]]>
     * </p>
     * <p>
     * The second special case is the "Location:" header. Not only does
     * it send this header back to the browser, but it also returns a
     * REDIRECT (302) status code to the browser
     * unless the 201 or
     * a 3xx status code has already been set.
     * </p>
     * <p>
     * ]]>
     * </p>
     * @param $bReplaceCurrent bool[optional] <p>
     * The optional replace parameter indicates
     * whether the header should replace a previous similar header, or
     * add a second header of the same type. By default it will replace,
     * but if you pass in false as the second argument you can force
     * multiple headers of the same type. For example:
     * </p>
     * <p>
     * ]]>
     * </p>
     * @param $iStatus int[optional] <p>
     * Forces the HTTP response code to the specified value.
     * </p>
     * @return void
     */
    public function send($bReplaceCurrent = null, $iStatus = null);

}