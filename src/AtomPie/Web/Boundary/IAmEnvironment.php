<?php
namespace AtomPie\Web\Boundary;

use AtomPie\Boundary\System\IAmEnvVariable;

interface IAmEnvironment extends IAmHttpCommunication
{

    /**
     * @return IAmEnvVariable
     */
    public function getEnv();

    /**
     * @return IAmSession
     */
    public function getSession();

    /**
     * @return IAmServer
     */
    public function getServer();

}