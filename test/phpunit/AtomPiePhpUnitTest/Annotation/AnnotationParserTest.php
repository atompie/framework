<?php
namespace AtomPiePhpUnitTest\Annotation;

use AtomPie\Annotation\AnnotationParser;
use AtomPiePhpUnitTest\Annotation\Mock\TestAnnotation;

class NoPhpDoc {

}

/**
 * Class MockAnnotationParser
 * @package AtomPiePhpUnitTest\Annotation
 * @TestAnnotation()
 * @TestAnnotation(param1 = "value1", param2 = "");
 * @TestAnnotation(multi_param1=" value1,value2 ", param2 = "value3");
 */
class MockAnnotationParser extends AnnotationParser
{

    public $iNoOfCalls = 0;

    public function parse($sPhpDoc)
    {
        $this->iNoOfCalls++;
    }
    /**
     * @TestAnnotation()
     * @TestAnnotation(param1 = "value1", param2 = "");
     * @TestAnnotation(multi_param1=" value1,value2 ", param2 = "value3");
     */
    public function method() {}
}

/**
 * Session test case.
 */
class AnnotationParserTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Checks if multi-param values can be parsed.
     *
     * @test
     */
    public function shouldParseAnnotationsFromPhpDoc()
    {
        $oParser = new AnnotationParser();
        $oAnnotations = $oParser->parse('
			/**
			 *
			 * Test description
			 * @EmptyAnnotation
			 * @Test1()
			 * @Test2(param1 = "value1", param2 = "");
			 * @Test3(multi_param1=" value1,value2 ", param2 = "value3");
			 */
		');

        /** @var $aAnnotationsTags \AtomPie\Annotation\AnnotationLine[] */
        $aAnnotationsTags = $oAnnotations['Test1'];
        $oAnnotation = array_pop($aAnnotationsTags);
        $this->assertTrue($oAnnotation->ClassName == 'Test1');
        $this->assertFalse($oAnnotation->Attributes->hasAttributes());

        $aAnnotationsTags = $oAnnotations['Test2'];
        $oAnnotation = array_pop($aAnnotationsTags);
        $this->assertTrue($oAnnotation->ClassName == 'Test2');
        $this->assertTrue($oAnnotation->Attributes->hasAttribute('param1'));
        $this->assertTrue($oAnnotation->Attributes->getAttribute('param1')->getValue() == 'value1');
        $this->assertTrue($oAnnotation->Attributes->hasAttribute('param2'));
        $this->assertTrue($oAnnotation->Attributes->getAttribute('param2')->getValue() == '');

        $aAnnotationsTags = $oAnnotations['EmptyAnnotation'];
        $oAnnotation = array_pop($aAnnotationsTags);
        $this->assertTrue($oAnnotation->ClassName == 'EmptyAnnotation');
        $this->assertFalse($oAnnotation->Attributes->hasAttributes());

        $aAnnotationsTags = $oAnnotations['Test3'];
        $oAnnotation = array_pop($aAnnotationsTags);
        $this->assertTrue($oAnnotation->ClassName == 'Test3');
        $this->assertTrue($oAnnotation->Attributes->hasAttribute('multi_param1'));
        $this->assertTrue($oAnnotation->Attributes->getAttribute('multi_param1')->getValue() == 'value1,value2');
        $this->assertTrue($oAnnotation->Attributes->hasAttribute('param2'));
        $this->assertTrue($oAnnotation->Attributes->getAttribute('param2')->getValue() == 'value3');
    }

    /**
     * @test
     */
    public function shouldReturnNullOnMissingAnnotationsFromPhpDoc()
    {
        $oParser = new AnnotationParser();
        $aAnnotations = $oParser->parse('
			/**
			 *
			 * Test description
			 */
		');
        $this->assertNull($aAnnotations);
    }

    /**
     * @test
     */
    public function shouldParseAnnotationsFromObjectPhpDoc()
    {
        $oParser = new AnnotationParser();
        $aAnnotations = $oParser->getAnnotationsFromObjectOrMethod(
            ['TestAnnotation' => TestAnnotation::class],
            MockAnnotationParser::class
        );
        $this->checkTestAnnotation($aAnnotations);

    }

    /**
     * @test
     */
    public function shouldParseAnnotationsFromReflectedObjectPhpDoc()
    {
        $oParser = new AnnotationParser();
        $aAnnotations = $oParser->getAnnotationsFromObjectOrMethod(
            ['TestAnnotation' => TestAnnotation::class],
            new \ReflectionClass(new MockAnnotationParser)
        );
        $this->checkTestAnnotation($aAnnotations);

    }

    /**
     * @test
     */
    public function shouldParseAnnotationsFromMethodPhpDoc()
    {
        $oParser = new AnnotationParser();
        $aAnnotations = $oParser->getAnnotationsFromObjectOrMethod(
            ['TestAnnotation' => TestAnnotation::class],
            new \ReflectionClass(new MockAnnotationParser),
            'method'
        );
        $this->checkTestAnnotation($aAnnotations);

    }

    /**
     * @test
     */
    public function shouldParseAnnotationsFromMethodPhpDoc1()
    {
        $oParser = new AnnotationParser();
        $aAnnotations = $oParser->getAnnotationsFromObjectOrMethod(
            ['TestAnnotation' => TestAnnotation::class],
            new MockAnnotationParser,
            'method'
        );
        $this->checkTestAnnotation($aAnnotations);

    }

    /**
     * @test
     */
    public function shouldNotParseAnnotationsFromObjectWithoutPhpDoc()
    {
        $oParser = new AnnotationParser();
        $aAnnotations = $oParser->getAnnotationsFromObjectOrMethod(
            ['TestAnnotation' => TestAnnotation::class],
            NoPhpDoc::class
        );
        $this->assertNull($aAnnotations);

    }

    /**
     * @test
     */
    public function shouldNotParseAnnotationsFromClosureWithoutPhpDoc()
    {
        $oParser = new AnnotationParser();
        $aAnnotations = $oParser->getAnnotationsFromObjectOrMethod(
            ['TestAnnotation' => TestAnnotation::class],
            new \ReflectionFunction(function() {

            })
        );
        $this->assertNull($aAnnotations);

    }

    /**
     * @test
     */
    public function shouldParseAnnotationsFromClosurePhpDoc()
    {
        $oParser = new AnnotationParser();
        $aAnnotations = $oParser->getAnnotationsFromObjectOrMethod(
            ['TestAnnotation' => TestAnnotation::class],

            new \ReflectionFunction(
                /**
                 * Class MockAnnotationParser
                 * @package AtomPiePhpUnitTest\Annotation
                 * @TestAnnotation()
                 * @TestAnnotation(param1 = "value1", param2 = "");
                 * @TestAnnotation(multi_param1=" value1,value2 ", param2 = "value3");
                 */
                function() {  }
            )
        );
        $this->checkTestAnnotation($aAnnotations);
    }

    /**
     * Tests parsing custom TestAnnotation.
     *
     * @test
     */
    public function shouldParseCustomTag()
    {
        $oParser = new AnnotationParser();
        $aAnnotations = $oParser->getAnnotations('
			/**
			 *
			 * Test description
			 * @TestAnnotation()
			 * @TestAnnotation(param1 = "value1", param2 = "");
			 * @TestAnnotation(param3=" value1,value2 ", param2 = "value3");
			 */
		', ['TestAnnotation' => TestAnnotation::class]
        );
        $this->assertTrue(count($aAnnotations[TestAnnotation::class]) == 3);
        /** @var TestAnnotation $oFirst */
        $oSecond = $aAnnotations[TestAnnotation::class][1];
        $this->assertTrue($oSecond->param1 == 'value1');
        $this->assertTrue($oSecond->param2 == '');

        $oThird = $aAnnotations[TestAnnotation::class][2];
        $this->assertTrue(!isset($oThird->param1));
        $this->assertTrue($oThird->param2 == 'value3');
        $this->assertTrue($oThird->param3 == 'value1,value2');
    }

    /**
     * @test
     */
    public function shouldParseOnceTheSamePhpDoc()
    {

        $oMock = $this
            ->getMockBuilder(AnnotationParser::class)
            ->setMethods(['parse'])
            ->getMock();

        $oMock->expects($this->exactly(1))->method('parse');

        /**
         * @var AnnotationParser $oMock
         */
        for ($i = 0; $i <= 10; $i++) {
            $oMock->getAnnotations('
			/**
			 *
			 * Test description
			 * @TestAnnotation()
			 * @TestAnnotation(param1 = "value1", param2 = "");
			 */
		', array(
                    'TestAnnotation' => get_class($oMock)
                )
            );
        }
    }

    /**
     * @param $aAnnotations
     */
    private function checkTestAnnotation(array $aAnnotations)
    {
        $this->assertTrue(!is_null($aAnnotations));
        $this->assertTrue(count($aAnnotations[TestAnnotation::class]) == 3);
        $this->assertTrue($aAnnotations[TestAnnotation::class][0] instanceof TestAnnotation);
        $this->assertTrue($aAnnotations[TestAnnotation::class][1] instanceof TestAnnotation);
        $this->assertTrue($aAnnotations[TestAnnotation::class][2] instanceof TestAnnotation);
    }
}