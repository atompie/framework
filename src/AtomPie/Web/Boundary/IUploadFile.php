<?php
namespace AtomPie\Web\Boundary;

use AtomPie\Web\Exception;

interface IUploadFile
{
    /**
     * Checks whether the uploaded file is valid.
     *
     * @param null $aValidFileFormats
     * @param null $iMaxFileSize
     * @throws Exception
     */
    public function isValid($aValidFileFormats = null, $iMaxFileSize = null);

    /**
     * Moves file to destination.
     *
     * @param $sDestinationFileName
     * @throws Exception
     */
    public function move($sDestinationFileName);

    public function getMime();

    public function getParamName();

    public function getTempName();

    public function getFileSize();
}