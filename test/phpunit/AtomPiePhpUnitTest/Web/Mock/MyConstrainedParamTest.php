<?php
namespace AtomPiePhpUnitTest\Web\Mock;

use AtomPie\Web\Connection\Http\Url\Param;
use AtomPie\Web\Connection\Http\Url\Param\IConstrain;

class MyConstrainedParamTest extends Param implements IConstrain
{

    /**
     * This method can throw Exception. Value is set in
     * $this->Value property. Remember Value can be array or string.
     *
     * @return bool
     * @throws \AtomPie\System\Exception
     */
    public function validate()
    {
        return false;
    }
}