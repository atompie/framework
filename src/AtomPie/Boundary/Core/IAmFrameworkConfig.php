<?php
namespace AtomPie\Boundary\Core;

use AtomPie\Boundary\System\IAmRouter;

interface IAmFrameworkConfig
{

    /**
     * @param $sNamespace
     * @return bool
     */
    public function hasEndPointNamespace($sNamespace);

    /**
     * @param $sNamespace
     * @return void
     */
    public function prependEndPointNamespace($sNamespace);
    
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
    public function getRootFolder();

    /**
     * @return string
     */
    public function getViewFolder();

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