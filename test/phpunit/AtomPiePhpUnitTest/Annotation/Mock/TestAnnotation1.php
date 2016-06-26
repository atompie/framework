<?php
namespace AtomPiePhpUnitTest\Annotation\Mock {

    use AtomPie\Annotation\AnnotationTag;

    class TestAnnotation1 extends AnnotationTag
    {

        /**
         * @return array
         */
        protected function getAllowedAttributes()
        {
            return array('param1', 'param2', 'param3');
        }
    }

}
