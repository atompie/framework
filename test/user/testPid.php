<?php
$oPid = new \AtomPie\Service\Process\Pid('/tmp');
if (!$oPid->isRunning()) {
    // Run
    sleep(3600);
} else {
    echo 'is running' . "\n";
}


