<?php
namespace CarloNicora\Minimalism\Services\AdvancedLogger\Database\Logger\Tables;

use CarloNicora\Minimalism\Services\MySQL\Abstracts\AbstractMySqlTable;
use CarloNicora\Minimalism\Services\MySQL\Interfaces\FieldInterface;

class LogsTable extends AbstractMySqlTable
{
    /** @var string  */
    protected static string $tableName = 'logs';

    /** @var array  */
    protected static array $fields = [
        'logId'         => FieldInterface::INTEGER
                        +  FieldInterface::PRIMARY_KEY
                        +  FieldInterface::AUTO_INCREMENT,
        'parentLogId'   => FieldInterface::INTEGER,
        'level'         => FieldInterface::INTEGER,
        'message'       => FieldInterface::STRING,
        'domain'        => FieldInterface::STRING,
        'context'       => FieldInterface::STRING,
        'userId'        => FieldInterface::INTEGER,
        'method'        => FieldInterface::STRING,
        'uri'           => FieldInterface::STRING,
        'time'          => FieldInterface::STRING,
    ];
}