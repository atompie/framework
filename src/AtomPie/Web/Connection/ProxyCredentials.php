<?php
namespace AtomPie\Web\Connection {

    class ProxyCredentials
    {

        public $Login;
        public $Password;

        public function __construct($Login, $Password)
        {
            $this->Login = $Login;
            $this->Password = $Password;
        }
    }
}

 