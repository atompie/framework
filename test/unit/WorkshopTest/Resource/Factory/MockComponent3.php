<?php
namespace WorkshopTest\Factory\Component {

    require_once __DIR__ . '/../Param/MyParam1.php';

    use WorkshopTest\Resource\Param\MyParam1;

    class MockComponent3
    {
        /**
         * @param \WorkshopTest\Resource\Component\MockComponent3 $oComponent
         * @param MyParam1 $AnnotatedParam1
         * @param MyParam1 $AnnotatedParam2
         */
        public function __factory(
            \WorkshopTest\Resource\Component\MockComponent3 $oComponent,
            MyParam1 $AnnotatedParam1,
            MyParam1 $AnnotatedParam2
        ) {
            $oComponent->oMyParam1 = $AnnotatedParam1;
            $oComponent->oMyParam2 = $AnnotatedParam2;
        }
    }
}
