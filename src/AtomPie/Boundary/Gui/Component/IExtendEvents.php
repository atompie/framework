<?php
namespace AtomPie\Boundary\Gui\Component {

    interface IExtendEvents
    {
        /**
         * Triggered even if component do not raise event.
         * It will be raised before all events in the component.
         *
         * @return void
         */
        public function __beforeEventInvoke();

        /**
         * Triggered if component raise event.
         *
         * @return void
         */
        public function __beforeEvent();

        /**
         * Triggered if component raise event.
         *
         * @param $mReturn
         * @return void
         */
        public function __afterEvent($mReturn);

        /**
         * Triggered even if component do not raise event.
         * It will be raised runAfter all events in the component.
         *
         * @return void
         */
        public function __afterEventInvoke();
    }
}
