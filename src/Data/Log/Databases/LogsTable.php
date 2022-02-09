<?php
namespace CarloNicora\Minimalism\Services\AdvancedLogger\Data\Log\Databases;

use CarloNicora\Minimalism\Services\MySQL\Data\SqlField;
use CarloNicora\Minimalism\Services\MySQL\Data\SqlTable;
use CarloNicora\Minimalism\Services\MySQL\Enums\FieldOption;
use CarloNicora\Minimalism\Services\MySQL\Enums\FieldType;

#[SqlTable(name: 'logs', databaseIdentifier: 'Logger')]
enum LogsTable
{
    #[SqlField(fieldType: FieldType::Integer,fieldOption: FieldOption::AutoIncrement)]
    case logId;

    #[SqlField(fieldType: FieldType::Integer)]
    case parentLogId;

    #[SqlField(fieldType: FieldType::Integer)]
    case level;

    #[SqlField]
    case message;

    #[SqlField]
    case domain;

    #[SqlField]
    case context;

    #[SqlField(fieldType: FieldType::Integer)]
    case userId;

    #[SqlField]
    case method;

    #[SqlField]
    case uri;

    #[SqlField(fieldOption: FieldOption::TimeCreate)]
    case time;
}