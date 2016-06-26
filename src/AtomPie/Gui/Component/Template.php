<?php
namespace AtomPie\Gui\Component {

    use AtomPie\Html;
    use AtomPie\System\Output\Buffer;

    class Template
    {

        const HEADER = '<!-- -AutoHead- -->';
        const BOTTOM = '<!-- -AutoBottom- -->';

        /**
         * @param string $sFile
         */
        private $sTemplateFile;

        /**
         * @var Html\Tag\Head
         */
        private $oHeader;

        /**
         * @var string
         */
        public $Content;

        /**
         * @var Html\ScriptsCollection
         */
        private $oScriptsCollection;

        public function __construct(
            $sTemplateFile,
            $sContent,
            $sEncoding,
            Html\Tag\Head $oHeader,
            Html\ScriptsCollection $oScriptsCollection
        ) {
            $this->sTemplateFile = $sTemplateFile;
            $this->Content = $sContent;
            $oHeader->setEncoding($sEncoding);
            $this->oHeader = $oHeader;
            $this->oScriptsCollection = $oScriptsCollection;
        }

        /**
         * @return string
         */
        public function render()
        {
            $sTemplate = $this->read();
            return str_replace(
                array(
                    Template::HEADER,
                    Template::BOTTOM,
                ),
                array(
                    $this->oHeader->__toString(),
                    $this->oScriptsCollection->__toString(),
                ),
                $sTemplate
            );
        }

        private function read()
        {

            Buffer::start();
            /** @noinspection PhpIncludeInspection */
            require($this->sTemplateFile);
            $sViewContent = Buffer::get();
            Buffer::end();

            return $sViewContent;
        }

    }
}

