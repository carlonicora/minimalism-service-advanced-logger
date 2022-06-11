<?php
namespace CarloNicora\Minimalism\Services\AdvancedLogger\Data\Log\Databases;

use CarloNicora\Minimalism\Interfaces\Sql\Attributes\SqlFieldAttribute;
use CarloNicora\Minimalism\Interfaces\Sql\Attributes\SqlTableAttribute;
use CarloNicora\Minimalism\Interfaces\Sql\Enums\SqlFieldOption;
use CarloNicora\Minimalism\Interfaces\Sql\Enums\SqlFieldType;

#[SqlTableAttribute(name: 'logs', databaseIdentifier: 'Logger')]
enum LogsTable
{
    #[SqlFieldAttribute(fieldType: SqlFieldType::Integer,fieldOption: SqlFieldOption::AutoIncrement)]
    case logId;

    #[SqlFieldAttribute(fieldType: SqlFieldType::Integer)]
    case parentLogId;

    #[SqlFieldAttribute(fieldType: SqlFieldType::Integer)]
    case level;

    #[SqlFieldAttribute(fieldType: SqlFieldType::String)]
    case message;

    #[SqlFieldAttribute(fieldType: SqlFieldType::String)]
    case domain;

    #[SqlFieldAttribute(fieldType: SqlFieldType::String)]
    case context;

    #[SqlFieldAttribute(fieldType: SqlFieldType::Integer)]
    case userId;

    #[SqlFieldAttribute(fieldType: SqlFieldType::String)]
    case method;

    #[SqlFieldAttribute(fieldType: SqlFieldType::String)]
    case uri;

    #[SqlFieldAttribute(fieldType: SqlFieldType::String, fieldOption: SqlFieldOption::TimeCreate)]
    case time;
}