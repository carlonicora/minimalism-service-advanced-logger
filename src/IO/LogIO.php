<?php
namespace CarloNicora\Minimalism\Services\AdvancedLogger\IO;

use CarloNicora\Minimalism\Services\AdvancedLogger\Data\Log;
use CarloNicora\Minimalism\Services\AdvancedLogger\Database\Logger\Tables\LogsTable;
use CarloNicora\Minimalism\Services\DataMapper\Abstracts\AbstractLoader;
use Exception;

class LogIO extends AbstractLoader
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
        return $this->returnSingleObject(
            recordset: $this->data->insert(
                tableInterfaceClassName: LogsTable::class,
                records: [$log->export()],
            ),
            objectType: Log::class,
        );
    }
}