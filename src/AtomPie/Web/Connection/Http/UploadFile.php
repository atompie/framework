<?php
namespace AtomPie\Web\Connection\Http {

    use AtomPie\Web\Boundary\IUploadFile;
    use AtomPie\Web\Exception;
    use AtomPie\I18n\Label;

    class UploadFile implements IUploadFile
    {

        private $aFile;
        private $bIsValid;

        /**
         * Pass url param name.
         *
         * @param $sParamName
         * @throws Exception
         */
        public function __construct($sParamName)
        {
            if (!isset($_FILES[$sParamName])) {
                throw new Exception(new Label('Invalid file parameter.'));
            }
            $this->aFile = $_FILES[$sParamName];
        }

        /**
         * Checks whether the uploaded file is valid.
         *
         * @param null $aValidFileFormats
         * @param null $iMaxFileSize
         * @throws Exception
         */
        public function isValid($aValidFileFormats = null, $iMaxFileSize = null)
        {
            if (!isset($this->aFile['error']) or is_array($this->aFile['error'])) {
                throw new Exception(new Label('Invalid parameters.'));
            }

            // Check $_FILES['upfile']['error'] value.
            switch ($this->aFile['error']) {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    throw new Exception(new Label('No file sent.'));
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new Exception(new Label('Exceeded file size limit.'));
                default:
                    throw new Exception(new Label('Unknown errors.'));
            }

            if (null !== $iMaxFileSize and $this->aFile['size'] > $iMaxFileSize) {
                throw new Exception('Exceeded file size limit.');
            }

            if (is_array($aValidFileFormats)) {
                // Check MIME Type.
                $oFileInfo = new \finfo(FILEINFO_MIME_TYPE);
                if (false === $ext = array_search(
                        $oFileInfo->file($this->aFile['tmp_name']),
                        $aValidFileFormats,
                        true
                    )
                ) {
                    throw new Exception(new Label('Invalid file format.'));
                }
            }
        }

        /**
         * Moves file to destination.
         *
         * @param $sDestinationFileName
         * @throws Exception
         */
        public function move($sDestinationFileName)
        {

            if (!$this->bIsValid) {
                throw new Exception(new Label('Validate file first.'));
            }

            if (!move_uploaded_file($this->aFile['tmp_name'], $sDestinationFileName)) {
                throw new Exception(new Label('Failed to move uploaded file.'));
            }

        }

        public function getMime()
        {
            return $this->aFile['type'];
        }

        public function getParamName()
        {
            return $this->aFile['name'];
        }

        public function getTempName()
        {
            return $this->aFile['tmp_name'];
        }

        public function getFileSize()
        {
            return $this->aFile['size'];
        }
    }

}
