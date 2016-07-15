<?php
namespace WorkshopTest;

require_once __DIR__ . '/../Config.php';

use AtomPie\Annotation\AnnotationParser;
use AtomPie\AnnotationTag\Authorize;
use AtomPie\AnnotationTag\Client;
use AtomPie\AnnotationTag\EndPoint;
use AtomPie\AnnotationTag\Header;
use AtomPie\AnnotationTag\Log;
use AtomPie\AnnotationTag\SaveState;

/**
 * Class Annotated
 * @package WorkshopTest
 * @Header(ContentType="application/json", Date="2015-01-01 00:00:12")
 * @Header(Server="MyServer")
 * @Authorize(ResourceIndex="WorkshopTest\Annotated", AuthType="Basic",AuthToken="risto:risto")
 */
class Annotated
{
    /**
     * @Header(Server="MyServer1")
     * @Header(ContentDisposition="inline; filename=test.txt")
     */
    public function annotated()
    {

    }
}

class ClassAnnotationsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function shouldParseClassAnnotationFromPhpDoc()
    {

        // Set default set of Annotations
        $aDefaultAnnotationMapping = array(
            'EndPoint' => EndPoint::class,
            'SaveState' => SaveState::class,
            'Header' => Header::class,
            'Client' => Client::class,
            'Authorize' => Authorize::class,
            'Log' => Log::class,
        );

        $oParser = new AnnotationParser();
        $oAnnotations = $oParser->getAnnotationsFromObjectOrMethod(
            $aDefaultAnnotationMapping,
            new Annotated()
        );

        $aHeaders = $oAnnotations->getFirstAnnotationByType(Header::class);
        foreach ($aHeaders as $oAnnotation) {
            if (isset($oAnnotation->ContentType)) {
                $this->assertTrue($oAnnotation->ContentType == 'application/json');
            }

            if (isset($oAnnotation->ContentDisposition)) {
                $this->assertTrue($oAnnotation->ContentDisposition == 'inline; filename=test.txt');
            }

            if (isset($oAnnotation->Server)) {
                $this->assertTrue($oAnnotation->Server == 'MyServer');
            }

            if (isset($oAnnotation->Date)) {
                $this->assertTrue($oAnnotation->Date == '2015-01-01 00:00:12');
            }

        }
    }

    /**
     * @test
     */
    public function shouldParseClassAnnotationFromPhpDocWhenCalledDouble()
    {

        // Set default set of Annotations
        $aDefaultAnnotationMapping = array(
            'Header' => Header::class,
            'Authorize' => Authorize::class
        );

        $oParser = new AnnotationParser();
        $oAnnotations = $oParser->getAnnotationsFromObjectOrMethod(
            $aDefaultAnnotationMapping,
            new Annotated()
        );

        $this->assertTrue($oAnnotations[Authorize::class][0] instanceof Authorize);

        $oAnnotations = $oParser->getAnnotationsFromObjectOrMethod(
            $aDefaultAnnotationMapping,
            new Annotated()
        );

        $this->assertTrue($oAnnotations[Authorize::class][0] instanceof Authorize);
    }
}
