<?php
namespace AtomPie\Web\SSL {

    class PeerVerification
    {

        public $SSlVerifyPeer = false;
        /**
         * 0: Don’t check the common name (CN) attribute
         * 1: Check that the common name attribute at least exists
         * 2: Check that the common name exists and that it matches the host name of the server
         */
        public $SSlVerifyHost;
        public $CaInfo;

    }

}