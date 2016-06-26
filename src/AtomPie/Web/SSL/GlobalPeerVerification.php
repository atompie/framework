<?php
namespace AtomPie\Web\SSL {

    class GlobalPeerVerification extends PeerVerification
    {

        public function __construct()
        {
            $this->SSlVerifyPeer = true;
            $this->SSlVerifyHost = 2;
            $this->CaInfo = __DIR__ . DIRECTORY_SEPARATOR . 'PeerInfo' . DIRECTORY_SEPARATOR . 'cacert.pem';
        }

    }
}