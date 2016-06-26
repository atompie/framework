<?php
namespace AtomPiePhpUnitTest\Html;

use AtomPie\Html\Attribute;
use AtomPie\Html\ElementNode;
use AtomPie\Html\Tag\Table;
use AtomPie\Html\Tag\TableColumn;

class HtmlTableTest extends \PHPUnit_Framework_TestCase
{

    public function testGenerateTable()
    {

        $aData = array(
            array('Column1' => 'Data1', 'Column2' => 'Data1'),
            array('Column1' => 'Data2', 'Column2' => 'Data2'),
            array('Column1' => 'Data3', 'Column2' => 'Data3'),
        );

        $oTable = new Table();
        $oTable->bind($aData);
        $oTable->addColumn('Column1', new TableColumn('Header1'));
        $oTable->addColumn('Column2', new TableColumn('Header2'));

        $this->assertTrue($oTable->__toString() == '<table><tr><th>Header1</th><th>Header2</th></tr><tr><td>Data1</td><td>Data1</td></tr><tr><td>Data2</td><td>Data2</td></tr><tr><td>Data3</td><td>Data3</td></tr></table>');

        $oHeaderTemplate = new ElementNode('th');
        $oHeaderTemplate->addAttribute(new Attribute('class', 'fillWith'));

        $oTable->setHeaderTemplate($oHeaderTemplate);

        $this->assertTrue($oTable->__toString() == '<table><tr><th class="fillWith">Header1</th><th class="fillWith">Header2</th></tr><tr><td>Data1</td><td>Data1</td></tr><tr><td>Data2</td><td>Data2</td></tr><tr><td>Data3</td><td>Data3</td></tr></table>');
    }


}