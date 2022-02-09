<?php
namespace CarloNicora\Minimalism\Services\AdvancedLogger\Interfaces;

use CarloNicora\Minimalism\Services\AdvancedLogger\Data\Log\DataObjects\Log;

interface LoggerCommandInterface
{
    /**
     * @param Log $log
     * @return Log
     */
    public function writeLog(
        Log $log,
    ): Log;

    /**
     * @param Log $log
     * @return bool
     */
    public function isLogLevelEnough(
        Log $log
    ): bool;
}