<?php
namespace AtomPiePhpUnitTest;

use AtomPie\Boundary\System\IAmEnvVariable;

class ApplicationConfig extends \AtomPie\Config\ApplicationConfig 
{
    public function __construct(IAmEnvVariable $oEnv)
    {
        $this->set('testKey','yes');
    }
}