<?php
namespace AtomPiePhpUnitTest\System;

use AtomPie\Boundary\Gui\Component\IHaveContext;
use AtomPie\Gui\Component\NamespaceValue;

class AComponent implements IHaveContext
{
    function getName()
    {
        return 'A';
    }

    /**
     * @return boolean
     */
    public function hasName()
    {
        return true;
    }

    /**
     * @return NamespaceValue
     */
    public function getNamespace()
    {
        return new NamespaceValue('root', 'MyNamespace');
    }
}

class NamespaceValueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldHandleInternalComponentNamespace()
    {
        $oA = new AComponent();
        $oNamespace = new NamespaceValue($oA->getNamespace(), $oA->getName());
        $this->assertTrue($oNamespace == 'root\MyNamespace\A');
    }


}
