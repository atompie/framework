<?php
namespace AtomPiePhpUnitTest\I18n;

use AtomPie\I18n\Label;

class LabelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldLabelAsText()
    {
        $oLabel = new Label('test');
        echo $oLabel->__toString();
    }
}
