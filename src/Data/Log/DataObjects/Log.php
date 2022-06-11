<?php
/** @noinspection UnusedConstructorDependenciesInspection */
/** @noinspection SenselessPropertyInspection */

namespace CarloNicora\Minimalism\Services\AdvancedLogger\Data\Log\DataObjects;

use CarloNicora\Minimalism\Enums\LogLevel;
use CarloNicora\Minimalism\Factories\ObjectFactory;
use CarloNicora\Minimalism\Factories\TimerFactory;
use CarloNicora\Minimalism\Interfaces\Sql\Attributes\DbField;
use CarloNicora\Minimalism\Interfaces\Sql\Attributes\DbTable;
use CarloNicora\Minimalism\Interfaces\Sql\Enums\DbFieldType;
use CarloNicora\Minimalism\Interfaces\Sql\Interfaces\SqlDataObjectInterface;
use CarloNicora\Minimalism\Interfaces\Sql\Traits\SqlDataObjectTrait;
use CarloNicora\Minimalism\Services\AdvancedLogger\Data\Log\Databases\LogsTable;

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

    /** @var int|null  */
    #[DbField]
    private int|null $parentLogId=null;

    /** @var LogLevel  */
    #[DbField(fieldType: DbFieldType::Custom)]
    private LogLevel $level;

    /** @var string  */
    #[DbField]
    private string $message;

    /** @var string|null  */
    #[DbField]
    private string|null $domain;

    /** @var array  */
    #[DbField(fieldType: DbFieldType::Array)]
    private array $context;

    /** @var int|null  */
    #[DbField]
    private ?int $userId = null;

    /** @var string|null  */
    #[DbField]
    private ?string $method;

    /** @var string|null  */
    #[DbField]
    private ?string $uri;

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
        private readonly ObjectFactory $objectFactory,
        LogLevel $logLevel=LogLevel::Info,
        string $message='',
        string|null $domain=null,
        array $context=[],
    )
    {
        $this->level = $logLevel;
        $this->message = $message;
        $this->domain = $domain;
        $this->context = $context;

        $this->context['duration'] = TimerFactory::elapse();

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
     * @return int
     */
    public function getId(
    ): int
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
     * @param int|null $userId
     */
    public function setUserId(
        int|null $userId=null,
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

        return LogLevel::from($value);
    }
}