<?php
namespace AtomPie\Boundary\Core;

use AtomPie\Boundary\System\IAmRouter;
use AtomPie\Boundary\System\IHandleException;
use AtomPie\Boundary\System\IRunAfterMiddleware;
use AtomPie\Boundary\System\IRunBeforeMiddleware;
use AtomPie\System\ContractFillers;

interface IAmFrameworkConfig
{

    /**
     * @return ISetUpContentProcessor[]
     */
    public function getContentProcessors();

    /**
     * @return IHandleException
     */
    public function getErrorHandler();
    
    /**
     * @return IRunAfterMiddleware[]|IRunBeforeMiddleware[]
     */
    public function getMiddleware();
    
    /**
     * @return ContractFillers
     */
    public function getContractsFillers();
        
    /**
     * @return array
     */
    public function getEndPointNamespaces();

    /**
     * @return array
     */
    public function getEndPointClasses();

    /**
     * @return array
     */
    public function getEventNamespaces();

    /**
     * @return array
     */
    public function getEventClasses();

    /**
     * @return string
     */
    public function getDefaultEndPoint();
    /**
     * @return mixed
     */
    public function getAppConfig();
    /**
     * @return IAmRouter
     */
    public function getRouter();
}