<?php
namespace {

    use AtomPie\Core\Config;
    use AtomPie\System\Kernel;
    use AtomPie\Web\Environment;

    $sVendorDir = __DIR__ . '/../../../../../../';
    $oLoader = require_once $sVendorDir. 'autoload.php';

    $oEnvironment = Environment::getInstance();

    $oKernel = new Kernel($oEnvironment, $oLoader);

    $sAtomPieConfigClosureFile = $sVendorDir. '../.atompie/boot.php';

    if(!is_file($sAtomPieConfigClosureFile)) {
        throw new Exception('File .atompie/boot.php doe not exist.');
    }

    /** @noinspection PhpIncludeInspection */
    $sConfigClosure = require $sAtomPieConfigClosureFile;
    $oConfig = $sConfigClosure($oEnvironment, $oKernel);
    
    $oResponse = $oKernel->boot($oConfig);
    $oResponse->send();

}