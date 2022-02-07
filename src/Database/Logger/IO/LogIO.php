<?php
namespace CarloNicora\Minimalism\Services\AdvancedLogger\Database\Logger\IO;

use CarloNicora\Minimalism\Interfaces\Sql\Abstracts\AbstractSqlIO;
use CarloNicora\Minimalism\Services\AdvancedLogger\DataObjects\Log;
use Exception;

class LogIO extends AbstractSqlIO
{
    /**
     * @param Log $log
     * @return Log
     * @throws Exception
     */
    public function insert(
        Log $log,
    ): Log
    {
        return $this->data->create(
            factory: $log,
            sqlObjectInterfaceClass: Log::class,
        );
    }
}