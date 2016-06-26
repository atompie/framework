<?php
namespace AtomPiePhpUnitTest\Event {

    use AtomPie\EventBus\EventHandler;
    use Generi\Boundary\ICollection;
    use Generi\Collection;

    class Raiser
    {

        use EventHandler;

        const EVENT = 'onTestEvent';

        /**
         * @var Collection
         */
        public $oCollection;

        public function raise()
        {
            $this->oCollection = new Collection();
            $this->triggerEvent(self::EVENT, $this->oCollection);
        }
    }

    /**
     * test case.
     */
    class EventHandlerTraitTest extends \PHPUnit_Framework_TestCase
    {

        /**
         * @test
         */
        public function shoudHandleEventRaise()
        {
            $oRaiser = new Raiser();

            // Closure
            /** @noinspection PhpUnusedParameterInspection */
            $oRaiser->handleEvent(Raiser::EVENT, function ($oSender, $oCollection) {
                /* @var $oCollection ICollection */
                $oCollection->add('OK', 'closure');
            });

            $oRaiser->raise();

            $this->assertTrue($oRaiser->oCollection->has('closure'));
        }

        /**
         * @test
         * @throws \AtomPie\EventBus\Exception
         */
        public function shouldHandleRemoveEvent()
        {
            $oRaiser = new Raiser();
            $oThat = $this;
            $iEventId = $oRaiser->handleEvent(Raiser::EVENT, function () use ($oThat) {
                $oThat->assertTrue(false);
            });
            // Static listener
            $oRaiser->handleEvent(Raiser::EVENT, function () use ($oThat) {
                $oThat->assertTrue(true);
            });
            // Remove callback
            $oRaiser->removeEventHandler('onTestEvent', $iEventId);
            $oRaiser->raise();

        }

    }
}

