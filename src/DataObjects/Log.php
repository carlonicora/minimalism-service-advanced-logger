<?php
/** @noinspection UnusedConstructorDependenciesInspection */
/** @noinspection SenselessPropertyInspection */

namespace CarloNicora\Minimalism\Services\AdvancedLogger\DataObjects;

use CarloNicora\Minimalism\Enums\LogLevel;
use CarloNicora\Minimalism\Factories\ObjectFactory;
use CarloNicora\Minimalism\Interfaces\Sql\Attributes\DbField;
use CarloNicora\Minimalism\Interfaces\Sql\Attributes\DbTable;
use CarloNicora\Minimalism\Interfaces\Sql\Enums\DbFieldType;
use CarloNicora\Minimalism\Interfaces\Sql\Interfaces\SqlDataObjectInterface;
use CarloNicora\Minimalism\Services\AdvancedLogger\Database\Logger\Tables\LogsTable;
use CarloNicora\Minimalism\Services\MySQL\Traits\SqlDataObjectTrait;

#[DbTable(tableClass: LogsTable::class)]
class Log implements SqlDataObjectInterface
{
    use SqlDataObjectTrait;

    /*
     * Log Levels used
     * LogLevel::Info -> Statistical
     * LogLevel::Error -> Unstable
     * LogLevel::Alert -> Unsecure
     * LogLevel::Emergency -> Unusable
     */

    /** @var int  */
    #[DbField]
    private int $logId;

    /** @var int  */
    #[DbField]
    private int $parentLogId;

    /** @var LogLevel  */
    #[DbField(fieldType: DbFieldType::Custom)]
    private LogLevel $level;

    /** @var string  */
    #[DbField]
    private string $message;

    /** @var string|null  */
    #[DbField]
    private ?string $domain;

    /** @var array  */
    #[DbField(fieldType: DbFieldType::Array)]
    private array $context;

    /** @var int  */
    #[DbField]
    private int $userId;

    /** @var string  */
    #[DbField]
    private string $method;

    /** @var string  */
    #[DbField]
    private string $uri;

    /** @var string  */
    #[DbField(fieldType: DbFieldType::IntDateTime)]
    private string $time;

    /**
     * @param ObjectFactory $objectFactory
     * @param LogLevel $logLevel
     * @param string $message
     * @param string|null $domain
     * @param array $context
     */
    public function __construct(
        private ObjectFactory $objectFactory,
        LogLevel $logLevel=LogLevel::Info,
        string $message='',
        ?string $domain=null,
        array $context=[],
    )
    {
        $this->level = $logLevel;
        $this->message = $message;
        $this->domain = $domain;
        $this->context = $context;

        $this->time = time();
        $this->uri = ($_SERVER['REQUEST_URI'] ?? null);
        $this->method = ($_SERVER['REQUEST_METHOD'] ?? null);
    }

    /**
     * @param int $parentLogId
     */
    public function setParentLogId(
        int $parentLogId,
    ): void
    {
        $this->parentLogId = $parentLogId;
    }

    /**
     * @return LogLevel
     */
    public function getLogLevel(
    ): LogLevel
    {
        return $this->level;
    }

    /**
     * @return string
     */
    public function getMessage(
    ): string
    {
        return $this->message;
    }

    /**
     * @return int|null
     */
    public function getId(
    ): ?int
    {
        return $this->logId;
    }

    /**
     * @param string $domain
     */
    public function setDomain(
        string $domain,
    ): void
    {
        $this->domain = $domain;
    }

    /**
     * @param int $userId
     */
    public function setUserId(
        int $userId,
    ): void
    {
        $this->userId = $userId;
    }

    /**
     * @param array $additionalContext
     * @return void
     */
    public function addContext(
        array $additionalContext,
    ): void
    {
        $this->context = array_merge($this->context, $additionalContext);
    }

    /**
     * @param string $fieldName
     * @param mixed|null $value
     * @return mixed
     * @noinspection PhpMixedReturnTypeCanBeReducedInspection
     */
    public function translateCustomField(
        string $fieldName,
        mixed $value = null,
    ): mixed
    {
        If ($value === null){
            return $this->level->value;
        }

        $this->level = LogLevel::from($value);

        return null;
    }
}