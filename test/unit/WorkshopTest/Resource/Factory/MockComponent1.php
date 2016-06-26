<?php
namespace WorkshopTest\Factory\Component {

    require_once __DIR__ . '/../Param/MyParam1.php';
    require_once __DIR__ . '/../Param/MyParam2.php';

    use WorkshopTest\Resource\Param\MyParam1;
    use WorkshopTest\Resource\Param\MyParam2;

    class MockComponent1
    {
        public function __factory(
            \WorkshopTest\Resource\Component\MockComponent1 $oComponent,
            MyParam1 $MyParam1,
            MyParam2 $MyParam2 = null
        ) {
            $oComponent->oMyParam1 = $MyParam1;
            $oComponent->oMyParam2 = $MyParam2;
        }
    }
}
