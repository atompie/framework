<?php
namespace AtomPiePhpUnitTest\Web;

use AtomPie\Web\Connection\Http\ParamConverter;

class ParamConverterTest extends \PHPUnit_Framework_TestCase
{

    public function testConvertArray()
    {

        $aData = array(
            'a' => array('Column1' => 'Data1', 'Column2' => 'Data1'),
            'b' => array('Column1' => 'Data2', 'Column2' => 'Data2'),
            'c' => array('Column1' => 'Data3', 'Column2' => 'Data3'),
        );

        $oConverter = new ParamConverter();
        $aResult = $oConverter->convertArray($aData);

        $this->assertTrue($aResult['[a][Column1]'] == 'Data1');
        $this->assertTrue($aResult['[a][Column2]'] == 'Data1');
        $this->assertTrue($aResult['[b][Column1]'] == 'Data2');
        $this->assertTrue($aResult['[b][Column2]'] == 'Data2');
        $this->assertTrue($aResult['[c][Column1]'] == 'Data3');
        $this->assertTrue($aResult['[c][Column2]'] == 'Data3');

        $aData = array();
        $oConverter = new ParamConverter();
        $aResult = $oConverter->convertArray($aData);
        $this->assertTrue(empty($aResult));

        $aData = array(
            'a',
            'b',
            'c' => 'c'
        );
        $oConverter = new ParamConverter();
        $aResult = $oConverter->convertArray($aData);

        $this->assertTrue($aResult['[0]'] == 'a');
        $this->assertTrue($aResult['[1]'] == 'b');
        $this->assertTrue($aResult['[c]'] == 'c');
    }


}