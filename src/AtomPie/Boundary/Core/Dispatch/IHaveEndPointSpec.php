<?php
namespace AtomPie\Boundary\Core\Dispatch {

    interface IHaveEndPointSpec
    {
        /**
         * @return IAmEndPointValue
         */
        public function getEndPoint();
    }


}
