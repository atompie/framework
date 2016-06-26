<?php
namespace AtomPie\View {

    use AtomPie\View\Boundary\IReadContent;
    use AtomPie\I18n\Label;
    use AtomPie\System\IO\File;

    class TwigTemplateFile implements IReadContent
    {

        private static $sViewRepository;
        private $sFile;

        public function __construct($mFile)
        {
            if ($mFile instanceof File) {
                $this->sFile = $mFile->getPath();
            } else {
                if (is_string($mFile)) {
                    $this->sFile = $mFile;
                } else {
                    throw new Exception('Invalid template path. Expected string or File object.');
                }
            }
        }

        /**
         * Feature: Caches view file reading.
         *
         * @return string
         */
        public function read()
        {

            if (!isset(self::$sViewRepository[$this->sFile])) {
                self::$sViewRepository[$this->sFile] = $this->readFile($this->sFile);
            }

            return self::$sViewRepository[$this->sFile];
        }

        /**
         * @param $sFile
         * @throws Exception
         * @return string
         */
        private function readFile($sFile)
        {

            if (is_null($sFile)) {
                throw new Exception(new Label('View file is empty'));
            } else {
                if (!is_file($sFile)) {
                    throw new Exception(
                        sprintf(
                            'View file [%s] could not be located.',
                            $sFile
                        )
                    );
                }
            }

            $oFile = new File($sFile);
            return $oFile->loadRaw();

        }


    }

}
