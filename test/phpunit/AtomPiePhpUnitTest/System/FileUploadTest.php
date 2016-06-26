<?php
namespace AtomPiePhpUnitTest\System;

use AtomPie\System\IO\File;
use AtomPie\Web\Connection\Http\Request;

class FileUploadTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function shouldReturnUploadedFileName()
    {
        $oRequest = new Request(Request\Method::POST);
        $oResponse = $oRequest->send(
            'http://192.168.100.100/php.atompie-project/src/Example/Public/Main.upload',
            [
                'file1' => new File(__DIR__ . '/Resource/uploadfile1.txt'),
                'file2' => new File(__DIR__ . '/Resource/uploadfile2.txt'),
                'param1' => 1
            ]
        );
        $oReturn = $oResponse->getContent()->decodeAsJson();
        $this->assertTrue($oReturn->file1->name == 'file1');
        $this->assertTrue($oReturn->file2->name == 'file2');
        $this->assertTrue($oReturn->file1->type == 'application/octet-stream');
        $this->assertTrue($oReturn->file2->type == 'application/octet-stream');
    }
}
