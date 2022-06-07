<?php
namespace CarloNicora\Minimalism\Services\AdvancedLogger\Data\Log\IO;

use CarloNicora\Minimalism\Interfaces\Sql\Abstracts\AbstractSqlIO;
use CarloNicora\Minimalism\Services\AdvancedLogger\Data\Log\DataObjects\Log;
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
            queryFactory: $log,
            responseType: Log::class,
        );
    }
}