<?php
namespace AtomPie\Html\Tag {

    use AtomPie\Html\ElementNode;
    use AtomPie\Html\Exception;
    use AtomPie\Html\TextNode;

    class Table extends ElementNode
    {

        /**
         * @var TableRow
         */
        public $RowTemplate;

        /**
         * @var TableColumn[]
         */
        public $Columns = array();

        /**
         * @var ElementNode
         */
        public $HeaderTemplate;

        /**
         * @var array
         */
        private $aData;

        public function __construct()
        {
            parent::__construct('table');
        }

        /**
         * @param array| \Iterator $aData
         */
        public function bind($aData)
        {
            $this->aData = $aData;
        }

        /**
         * Closure can manipulate column data.
         *
         * @param $sColumnName
         * @param TableColumn $oColumn
         * @param \Closure $oClosure
         */
        public function addColumn($sColumnName, TableColumn $oColumn, \Closure $oClosure = null)
        {
            if (null !== $oClosure) {
                $oColumn->setClosure($oClosure);
            }
            $this->Columns[$sColumnName] = $oColumn;
        }

        /**
         * @param $sColumnName
         * @param $sUrl
         * @return Link
         * @throws Exception
         */
        public function addColumnSorting($sColumnName, $sUrl)
        {
            if (!isset($this->Columns[$sColumnName])) {
                throw new Exception(sprintf('Column [%s] does not exist.', $sColumnName));
            }

            /** @var TableColumn $oColumn */
            $oColumn = $this->Columns[$sColumnName];
            $oColumn->HeaderName = new Link($sUrl, $oColumn->HeaderName);

            return $oColumn->HeaderName;
        }

        /**
         * @param TableRow $oRowTemplate
         */
        public function setRowTemplate(TableRow $oRowTemplate)
        {
            $this->RowTemplate = $oRowTemplate;
        }

        /**
         * @param ElementNode $oHeaderTemplate
         */
        public function setHeaderTemplate(ElementNode $oHeaderTemplate)
        {
            $this->HeaderTemplate = $oHeaderTemplate;
        }

        protected function beforeToString()
        {

            if (!isset($this->RowTemplate)) {
                $this->RowTemplate = new TableRow();
            }

            if (!isset($this->HeaderTemplate)) {
                $this->HeaderTemplate = new ElementNode('th');
            }

            $oRow = clone $this->RowTemplate;
            foreach ($this->Columns as $oColumn) {
                $oHeaderTemplate = clone $this->HeaderTemplate;
                if ($oColumn->HeaderName instanceof ElementNode) {
                    $oHeaderTemplate->addChild($oColumn->HeaderName);
                } else {
                    $oHeaderTemplate->addChild(new TextNode($oColumn->HeaderName));
                }
                $oRow->addChild($oHeaderTemplate);
            }

            $this->addChild($oRow);
            reset($this->Columns);

            if ((is_array($this->aData) || $this->aData instanceof \Iterator) && !empty($this->aData)) {
                foreach ($this->aData as $aRecordData) {

                    $oRow = clone $this->RowTemplate;

                    foreach ($this->Columns as $sKey => $oColumnTemplate) {
                        $oColumn = clone $oColumnTemplate;
                        $oColumn->removeChildren();
                        $sValue = $oColumn->runClosure($aRecordData, $sKey);
                        $oColumn->addChild(new TextNode($sValue));

                        $oRow->addChild($oColumn);
                    }
                    $this->addChild($oRow);
                }
            }

        }

        protected function afterToString()
        {
            $this->removeChildren();
        }


    }

}