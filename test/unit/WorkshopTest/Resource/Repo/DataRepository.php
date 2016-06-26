<?php
namespace WorkshopTest\Resource\Repo {

    use AtomPie\Boundary\Core\IAmService;

    class DataRepository implements IAmService
    {
        public function loadData()
        {
            return array('data');
        }

        public function __construct()
        {
        }
    }

}
