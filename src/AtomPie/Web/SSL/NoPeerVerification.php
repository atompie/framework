<?php
namespace AtomPie\Web\SSL {

    class NoPeerVerification extends PeerVerification
    {

        public function __construct()
        {
            $this->SSlVerifyPeer = false;
            $this->SSlVerifyHost = false;
        }

    }

}