<?php
namespace WorkshopTest\Resource {

    use AtomPie\Boundary\Core\IAmService;
    use Generi\Object;

    class Service extends Object implements IAmService
    {
        final public function __construct()
        {
            // All services mus have parameter-less constructor
        }
    }

}
