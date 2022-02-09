<?php
namespace CarloNicora\Minimalism\Services\AdvancedLogger\Commands;

use CarloNicora\Minimalism\Interfaces\SimpleObjectInterface;
use CarloNicora\Minimalism\Services\AdvancedLogger\Data\Log\DataObjects\Log;
use CarloNicora\Minimalism\Services\AdvancedLogger\Data\Log\IO\LogIO;
use CarloNicora\Minimalism\Services\AdvancedLogger\Interfaces\LoggerCommandInterface;
use Exception;

class MySqlLoggerCommand implements LoggerCommandInterface, SimpleObjectInterface
{
    /**
     * @param LogIO $logIO
     */
    public function __construct(
        private LogIO $logIO,
    )
    {
    }

    /**
     * @param Log $log
     * @return bool
     */
    public function isLogLevelEnough(
        Log $log,
    ): bool
    {
        return true;
    }

    /**
     * @param Log $log
     * @return Log
     * @throws Exception
     */
    public function writeLog(
        Log $log,
    ): Log
    {
        return $this->logIO->insert(log: $log);
    }
}