<?php
namespace AtomPiePhpUnitTest\Gui;

use AtomPie\Web\Environment;
use AtomPiePhpUnitTest\Config;
use WorkshopTest\Boot;
use WorkshopTest\RequestFactory;

class TemplateAnnotationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * MockComponent13 has ContentType text/html
     *
     * @test
     */
    public function shouldThrowExceptionAboutLackingTemplateForRegularContentType()
    {

        RequestFactory::produce('MockComponent13');
        Environment::destroyInstance();
        $oResponse = Boot::run(Boot::getEnv(), Config::get());
        $sContent = $oResponse->getContent()->get();
        $this->assertTrue(false !== strstr($sContent, 'Template path not defined in component'));

    }

    /**
     * MockComponent12 has ContentType application/xml so no template needed.
     *
     * @test
     */
    public function shouldNotThrowExceptionAboutLackingTemplateForSerializedContent()
    {

        RequestFactory::produce(
            'MockComponent12'
        );

        $oResponse = Boot::run(Boot::getEnv(), Config::get());
        $oResponseXml = $oResponse->getContent()->getAsSimpleXml();

        /** @noinspection PhpUndefinedFieldInspection */
        $this->assertTrue($oResponseXml->Property1 == 1);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->assertTrue($oResponseXml->Property2 == 2);

    }
}
