<?php
namespace AtomPie\Boundary\Gui\Component {

    use AtomPie\View\Boundary\ICanBeRendered;

    interface IAmComponent extends
        IHaveEvents,
        ICanBeProcessed,
        ICanBeFactored,
        ICanBeRendered
    {

        // Tree

        /**
         * @return void
         */
        public function markTreeClean();

        /**
         * @return boolean
         */
        public function isTreeDirty();


    }

}
