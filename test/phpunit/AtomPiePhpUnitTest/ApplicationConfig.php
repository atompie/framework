<?php
namespace AtomPiePhpUnitTest;

use AtomPie\Boundary\System\IAmEnvVariable;

class ApplicationConfig extends \AtomPie\Core\ApplicationConfig 
{
    public function __construct(IAmEnvVariable $oEnv)
    {
        $this->set('testKey','yes');
    }
}