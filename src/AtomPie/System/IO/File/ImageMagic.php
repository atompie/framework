<?php
namespace AtomPie\System\IO\File {

    use AtomPie\System\Exception;

    class ImageMagic
    {
        /**
         *
         * @var string
         */
        private $sDestinationImagePath;
        /**
         *
         * @var Image
         */
        private $oImage;
        /**
         *
         * @var \Imagick
         */
        private $oImageMagic;

        /**
         *
         * @param Image $oImage
         * @return \AtomPie\System\IO\File\ImageMagic
         */
        public function setImage(Image $oImage)
        {
            $this->oImage = $oImage;
            return $this;
        }

        /**
         *
         * @param string $sDestinationImagePath
         * @return \AtomPie\System\IO\File\ImageMagic
         */
        public function setDestination($sDestinationImagePath)
        {
            $this->sDestinationImagePath = $sDestinationImagePath;
            return $this;
        }

        /**
         *
         * @param int $iToWidth
         * @param int $iToHeight
         * @return \AtomPie\System\IO\File\ImageMagic
         */
        public function scaleTo($iToWidth = null, $iToHeight = null)
        {
            if ($this->checkIfAlreadyDone()) {
                return $this;
            }

            $this->checkPrerequisites();

            $this->setupGeometry();
            $iWidthDiff = $iToWidth / $this->oImage->Width;
            $iHeightDiff = $iToHeight / $this->oImage->Height;

            if ($iWidthDiff >= $iHeightDiff) {
                $iWidth = $iToWidth;
                $iHeight = $this->oImage->Height * $iWidthDiff;
            } else {
                $iWidth = $this->oImage->Width * $iHeightDiff;
                $iHeight = $iToHeight;
            }

            // Get offsets
            $x = ($iWidth - $iToWidth) / 2;
            $y = ($iHeight - $iToHeight) / 2;

            // Resize and offset image
            $oImageProcessor = $this->getImageProcessor();

            $oImageProcessor->adaptiveResizeImage($iWidth, $iHeight);
            $oImageProcessor->extentImage($iToWidth, $iHeight, -$x, -$y);

            return $this;
        }

        /**
         *
         * @return Image
         */
        public function commit()
        {
            if ($this->checkIfAlreadyDone()) {
                return self::createImage($this->sDestinationImagePath);
            }

            $oImageProcessor = $this->getImageProcessor();

            $bRes = $oImageProcessor->writeImage($this->sDestinationImagePath);
            $oImageProcessor->clear();
            $oImageProcessor->destroy();

            if ($bRes) {
                return self::createImage($this->sDestinationImagePath);
            }

            return false;
        }

        /**
         *
         * @param Image $oImage
         * @param int $iWidth
         * @param int $iHeight
         * @return string
         */
        public static function generateFilenameForScaledImage(Image $oImage, $iWidth, $iHeight)
        {
            $sScaledFileNameExt = $oImage->getExtension();
            $sScaledFileNamePrefix = $oImage->getPath();

            return $sScaledFileNamePrefix . '_' . $iWidth . '_' . $iHeight . '.' . $sScaledFileNameExt;
        }

        /**
         *
         * @param string $sRootFolder
         * @param string $sDestinationFile
         * @return mixed
         */
        public static function cutRootFolder($sRootFolder, $sDestinationFile)
        {
            return str_replace($sRootFolder, '', $sDestinationFile);
        }

        /**
         *
         * @return boolean
         */
        private function checkIfAlreadyDone()
        {
            return is_file($this->sDestinationImagePath);
        }

        /**
         * Checks if image and destination folder is set;
         *
         * @throws Exception
         */
        private function checkPrerequisites()
        {
            if (!isset($this->oImage)) {
                throw new Exception('Set image first!');
            }

            if (!isset($this->sDestinationImagePath)) {
                throw new Exception('Set destination image first!');
            }
        }

        private function setupGeometry()
        {
            if ($this->oImage->isValid()) {
                $aSize = $this->getImageProcessor()->getImageGeometry();
                $this->oImage->Width = (int)$aSize['width'];
                $this->oImage->Height = (int)$aSize['height'];
            } else {
                throw new Exception('File [' . $this->oImage->getPath() . '] not found.');
            }
        }

        /**
         *
         * @return \Imagick
         */
        private function getImageProcessor()
        {
            if (!isset($this->oImageMagic)) {
                $this->oImageMagic = new \Imagick($this->oImage->getPath());
            }
            return $this->oImageMagic;
        }

        /**
         *
         * @param string $sPath
         * @return Image
         */
        private function createImage($sPath)
        {
            return new Image($sPath);
        }
    }
}