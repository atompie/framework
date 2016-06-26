<?php
namespace AtomPieTestAssets\Resource\Mock {

    use AtomPie\Html\Tag\Link;
    use AtomPie\Web\Boundary\IAmEnvironment;
    use AtomPie\Web\Environment;
    use AtomPie\Gui\Component;
    use AtomPie\Web\Connection\Http\Url\Param;
    use AtomPie\Gui\Component\Annotation\Tag\Template;

    /**
     * @property MockComponent10 LoginForm
     * @property array $List
     * @property array ArrayOfComponents
     * @Template(File="Resource/Theme/MockComponent9.mustache")
     * Class MockComponent7
     * @package WorkshopTest\Resource\Component
     */
    class MockComponent9 extends Component
    {

        public $Environment;

        public function __create(
            IAmEnvironment $Environment
        ) {
            $this->Environment = $Environment;

            $this->LoginForm = (new MockComponent10)->filledWith(['SimpleData' => 'test']);
            $this->ArrayOfComponents = [
                (new MockComponent10)->filledWith([
                    'SimpleData' => 'test1',
                    'List' => [
                        new MockComponent8(),
                        new MockComponent8(),
                        new MockComponent8(),
                    ]
                ]),
                (new MockComponent10)->filledWith(['SimpleData' => 'test2'])
            ];
            // TODO make better
            (new Component\Template\PlaceHolder($this, 'List'))
                ->has(MockComponent10::class)
                ->filledWith(
                    [
                        ['SimpleData' => 'test1', 'List' => new MockComponent8(),],
                        ['SimpleData' => 'test2'],
                        ['SimpleData' => (string)new Link('http://www.google.com', 'link')]
                    ]
                );

        }

    }

}

