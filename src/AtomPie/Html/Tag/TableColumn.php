<?php
namespace AtomPie\Html\Tag {

    use Generi\Exception;
    use AtomPie\Html\ElementNode;

    class TableColumn extends ElementNode
    {

        public $HeaderName;
        private $oClosure;

        public function __construct($sHeaderName)
        {
            parent::__construct('td');
            if ($sHeaderName instanceof ElementNode) {
                $this->HeaderName = $sHeaderName;
            } else {
                $this->HeaderName = $sHeaderName;
            }
        }

        public function setClosure(\Closure $oClosure = null)
        {
            $this->oClosure = $oClosure;
        }

        public function runClosure($mRecordData, $sKey)
        {

            if (isset($this->oClosure)) {

                $pFunction = $this->oClosure;
                $mRecordValue = $this->getValue($mRecordData, $sKey);
                return $pFunction($this, $mRecordData, $mRecordValue);

            } else {
                return $this->getValue($mRecordData, $sKey);
            }

        }

        private function getValue($mRecordData, $sKey)
        {
            if (is_array($mRecordData)) {
                $mRecordValue = isset($mRecordData[$sKey]) ? $mRecordData[$sKey] : '';
            } else {
                if (is_object($mRecordData)) {
                    $mRecordValue = isset($mRecordData->$sKey) ? $mRecordData->$sKey : '';
                } else {
                    throw new Exception('Incorrect data structure.');
                }
            }

            return $mRecordValue;
        }

    }

}