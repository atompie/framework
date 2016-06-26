<?php
namespace AtomPie\Web\Connection\Http {

    //TODO: Refactor to use request, this is kind of request

    use AtomPie\Web\Exception;

    class Upload
    {

        private $sUploadFieldName;

        public function __construct($sUploadFieldName)
        {
            $this->sUploadFieldName = $sUploadFieldName;
        }

        public function readFile()
        {

            $sTmpFileName = $_FILES[$this->sUploadFieldName]['tmp_name'];

            if ($this->isFileNotTooBig()) {
                throw new Exception('File is to big!');
            }

            return file_get_contents($sTmpFileName);

        }

        public function isUploadReady()
        {
            return isset($_FILES[$this->sUploadFieldName]) && is_uploaded_file($_FILES[$this->sUploadFieldName]['tmp_name']);
        }

        public function moveFileTo($sDestinationPath)
        {

            if (trim(strtolower($this->getUploadedFileExt())) == 'x-php') {
                throw new Exception('Could not move uploaded *.php file! PHP files are not allowed to be uploaded!');
            }

            if (trim(strtolower(substr($sDestinationPath, -3))) == 'php') {
                throw new Exception('Could not move uploaded *.php file! PHP files are not allowed to be uploaded!');
            }

            $sTmpFileName = $_FILES[$this->sUploadFieldName]['tmp_name'];

            if ($this->isFileNotTooBig()) {
                throw new Exception('File is to big!');
            }

            if (false == move_uploaded_file($sTmpFileName, $sDestinationPath)) {
                throw new Exception('Could not move uploaded file');
            }
            return true;
        }

        public function isFileNotTooBig()
        {
            return self::getMaxFileSize() < $this->getUploadedFileSize();
        }

        public function getUploadedFileMime()
        {
            return $_FILES[$this->sUploadFieldName]['type'];
        }

        public function getUploadedFileExt()
        {
            $sMime = $_FILES[$this->sUploadFieldName]['type'];
            $aMime = explode('/', $sMime);
            if (count($aMime) == 2) {
                return $aMime[1];
            }
            return '';
        }

        public function getUploadedFileName()
        {
            return isset($_FILES[$this->sUploadFieldName]) && isset($_FILES[$this->sUploadFieldName]['name'])
                ? trim($_FILES[$this->sUploadFieldName]['name'])
                : null;
        }

        public function getUploadedFileSize()
        {
            return (int)$_FILES[$this->sUploadFieldName]['size'];
        }

        public static function getMaxFileSize()
        {
            $iMaxFileSize = 1024 * 1024 * 24;    //24MB

            $sMaxUpload = @ini_get('upload_max_filesize');

            if (isset($sMaxUpload) && strchr($sMaxUpload, 'M')) {
                $iMb = (string)1024 * 1000;
                $sMaxUpload = (int)str_replace('M', '', $sMaxUpload);
                $sMaxUpload = $sMaxUpload * $iMb;

                return $sMaxUpload;
            }

            return $iMaxFileSize;
        }
    }
}