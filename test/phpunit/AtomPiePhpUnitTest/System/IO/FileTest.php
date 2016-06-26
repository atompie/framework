<?php
namespace AtomPiePhpUnitTest\System\IO;

use AtomPie\System\IO\File;

class FileTest extends \PHPUnit_Framework_TestCase
{

    public function testFile_CreateLoad_Raw()
    {
        $oFile = new File('/tmp/myfile.txt');
        $oFile->save('test');
        $this->assertTrue($oFile->loadRaw() == 'test');
        $oFile->remove();
    }

    public function testFile_CreateLoad_Utf8()
    {
        $oFile = new File('/tmp/myfile.txt');
        $oFile->save('łóśćęńźą');
        $this->assertTrue($oFile->load() == 'łóśćęńźą');
        $oFile->remove();
    }

    public function testFile_Append()
    {
        $oFile = new File('/tmp/myfile.txt');
        $oFile->save('test');


        $oFile = new File('/tmp/myfile.txt');
        $oFile->save('1', true);

        $this->assertTrue($oFile->loadRaw() == 'test1');
        $oFile->remove();
    }

    public function testFile_Rename_Exists()
    {
        $oFile = new File('/tmp/myfile.txt');
        $oFile->save('łóśćęńźą');
        $oFile->rename('/tmp/myfile1.txt');
        $this->assertTrue(is_file('/tmp/myfile1.txt'));

        $oFile = new File('/tmp/myfile1.txt');
        $this->assertTrue($oFile->isValid());

        $oFile->remove();
        $this->assertFalse($oFile->isValid());
    }

    public function testFile_CheckSum()
    {
        $oFile = new File('/tmp/myfile.txt');
        $oFile->save('łóśćęńźą');
        $this->assertTrue($oFile->getChecksum() == 'DDA2AC74');
        $oFile->remove();
    }

    public function testFile_BaseName()
    {
        $oFile = new File('/tmp/myfile.txt');
        $oFile->save('łóśćęńźą');
        $this->assertTrue($oFile->getBasename() == 'myfile.txt');
        $oFile->remove();
    }

    public function testFile_Extension()
    {
        $oFile = new File('/tmp/myfile.txt');
        $oFile->save('łóśćęńźą');
        $this->assertTrue($oFile->getExtension() == 'txt');
        $oFile->remove();
    }

    public function testFile_Name()
    {
        $oFile = new File('/tmp/myfile.txt');
        $oFile->save('łóśćęńźą');
        $this->assertTrue($oFile->getName() == 'myfile');
        $oFile->remove();
    }

    public function testFile_Permisstions()
    {
        $oFile = new File('/tmp/myfile.txt');
        $oFile->save('łóśćęńźą');
        $this->assertTrue($oFile->getUnixPermissions() == '-rw-rw-r--');
        $oFile->remove();
    }

    public function testFile_Path_Size()
    {
        $oFile = new File('/tmp/myfile.txt');
        $oFile->save('łóśćęńźą');
        $this->assertTrue($oFile->getPath() == '/tmp/myfile.txt');
        $this->assertTrue($oFile->getSize() == 16); // UTF-8
        $oFile->remove();

        $oFile = new File('/tmp/myfile.txt');
        $oFile->save('12345');
        $this->assertTrue($oFile->getPath() == '/tmp/myfile.txt');
        $this->assertTrue($oFile->getSize() == 5); // Not-UTF-8
        $oFile->remove();
    }

    public function testFile_Owner_Group()
    {
        $oFile = new File('/tmp/myfile.txt');
        $oFile->save('łóśćęńźą');
        $this->assertTrue($oFile->getOwner() == @fileowner('/tmp/myfile.txt'));
        $this->assertTrue($oFile->getGroup() == @filegroup('/tmp/myfile.txt'));
        $oFile->remove();
    }

}
