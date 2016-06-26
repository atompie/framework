<?php
namespace AtomPie\Html\Boundary {

    interface IAutoIndex extends IHaveIndex
    {
        public function setAutoIndex($bAutoIndex = true);

        public function hasAutoIndex();
    }

}

