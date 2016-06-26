<?php
namespace WorkshopTest\Factory\Component {

    require_once __DIR__ . '/../Param/MyParam1.php';

    use WorkshopTest\Resource\Param\MyParam1;

    class MockComponent2
    {
        public function __factory(
            \WorkshopTest\Resource\Component\MockComponent2 $oComponent,
            MyParam1 $MyParam1 = null
        ) {
            $oComponent->oMyParam1 = $MyParam1;
        }
    }
}
