<?php
namespace AtomPie\Service\Monitoring;

interface IAmProfileMessage
{
    /**
     * Log message
     *
     * @return mixed
     */
    public function log();

    /**
     * Set application start micro time if you want to profile execution
     * times.
     *
     * @param $iStartTime
     * @return mixed
     */
    public function measureTimeFrom($iStartTime);
}