<?php
namespace AtomPie\Core\Annotation\Tag {

    use AtomPie\Annotation\AnnotationTag;

    /**
     * @Annotation
     * @Target("METHOD")
     */
    final class SaveState extends AnnotationTag
    {

        /**
         * @var String
         */
        public $Param;

        /**
         * Default value: As="Value"
         *
         * ENUM "Value" saves state in that order
         *
         * Given I have made request 3 times with parameter
         *  ?ParentId[1]=1
         *  ?ParentId[2]=2
         *  ?ParentId[3]=-1
         *
         * State will equal:
         *
         *  array (size=3)
         *      1 => string '1' (length=1)
         *      2 => string '2' (length=1)
         *      3 => string '-1' (length=2)
         *
         * If I pass parameter 3 times as value na array:
         *
         *    ?ParentId=1
         *  ?ParentId=2
         *  ?ParentId=-1
         *
         * I will have:
         *
         *  state = -1 (one value)
         *
         * ENUM "Collection" saves state in that order
         *
         * Given I have made request 5 times with parameter
         *
         *    ?ParentId[a]=1
         *  ?ParentId[b]=2
         *  ?ParentId[b]=-3
         *  ?ParentId=-1
         *  ?ParentId[][a]=-1
         *
         * The keys will be ignored and I get collection (vector of values). Every new request I get new
         * value in collection.
         *
         *   state = array (size=3)
         *      0 => string '1' (length=1)
         *      1 => string '2' (length=1)
         *      2 => string '-3' (length=2)
         *      3 => string '-1' (length=2)
         *      4 => array(size=1)
         *              'a' => string '-1' (length=2)
         *
         *
         * ENUM "DistinctCollection" saves state in that order
         *
         *    Given I have made request 5 times with parameter
         *
         *    ?ParentId=1
         *  ?ParentId=2
         *  ?ParentId=3
         *
         * State will treat value as key.
         * State equals:
         *
         *    state = array (size=3)
         *      1 => string '1' (length=1)
         *      2 => string '2' (length=1)
         *      3 => string '3' (length=2)
         *
         * if I make more requests but with keys defined.
         *
         *  ?ParentId[a]=3
         *  ?ParentId[1]=3
         *
         * The keys will NOT be ignored and I get collection with some values replaced.
         *
         *   state = array (size=3)
         *      1 => string '3' (length=1)
         *      2 => string '2' (length=1)
         *      3 => string '3' (length=2)
         *      'a' => string '3' (length=2)
         *
         * NOTICE that first value was replaced.
         *
         * @Enum[{"Value","Collection","DistinctCollection"}]
         */
        public $As = "Value";

        /**
         * @return array
         */
        protected function getAllowedAttributes()
        {
            return array('Param', 'As');
        }

        /**
         * @param $sVariableName
         * @return bool
         */
        public function isPersistentParam($sVariableName)
        {
            return $this->Param == $sVariableName;
        }
    }

}
