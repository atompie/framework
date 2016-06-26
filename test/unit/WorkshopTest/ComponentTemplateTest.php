<?php
namespace WorkshopTest;

use AtomPie\Gui\Component;
use WorkshopTest\Resource\Component\MockComponentWithTemplate;

class ComponentTemplateTest extends \PHPUnit_Framework_TestCase
{

    public function testTemplateSettingUp()
    {
        $oComponent = new MockComponentWithTemplate();
        $this->assertTrue($oComponent->getTemplateFile(__DIR__)->getBasename() == 'Template.mustache');
    }

}
