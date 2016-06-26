<?php
namespace AtomPie\Core\Dispatch {

    use AtomPie\Boundary\Core\Dispatch\IAmEndPointUrl;
    use AtomPie\Web\Connection\Http\ImmutableUrl;

    class EndPointUrl extends ImmutableUrl implements IAmEndPointUrl
    {
    }

}
