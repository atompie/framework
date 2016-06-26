<?php
namespace WorkshopTest\Resource\EndPoint {

    use Generi\Object;

    class ErrorController extends Object
    {

        public function indexAction()
        {
            throw new \Exception('Error');
        }

    }

}
