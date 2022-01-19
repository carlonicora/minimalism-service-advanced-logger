<?php
namespace CarloNicora\Minimalism\Services\AdvancedLogger\Data;

use CarloNicora\Minimalism\Enums\LogLevel;
use CarloNicora\Minimalism\Factories\ObjectFactory;
use CarloNicora\Minimalism\Services\DataMapper\Abstracts\AbstractDataObject;
use Exception;

class Log extends AbstractDataObject
{
    /*
     * Log Levels used
     * LogLevel::Info -> Statistical
     * LogLevel::Error -> Unstable
     * LogLevel::Alert -> Unsecure
     * LogLevel::Emergency -> Unusable
     */

    /** @var string|null  */
    private ?string $verb;

    /** @var string|null  */
    private ?string $uri;

    /** @var int  */
    private int $time;

    /** @var int|null  */
    private ?int $parentLogId=null;

    /**
     * @param ObjectFactory $objectFactory
     * @param array|null $data
     * @param LogLevel $logLevel
     * @param int|null $id
     * @param int|null $userId
     * @param string $message
     * @param string|null $domain
     * @param array $context
     */
    public function __construct(
        ObjectFactory $objectFactory,
        ?array $data = null,
        private LogLevel $logLevel=LogLevel::Info,
        private ?int $id=null,
        private ?int $userId=null,
        private string $message='',
        private ?string $domain=null,
        private array $context=[],
    )
    {
        parent::__construct(
            objectFactory: $objectFactory,
            data: $data,
        );

        $this->time = time();
        $this->uri = ($_SERVER['REQUEST_URI'] ?? null);
        $this->verb = ($_SERVER['REQUEST_METHOD'] ?? null);
    }

    public function import(
        array $data,
    ): void
    {
        $this->id = $data['logId'];
        $this->logLevel = LogLevel::from($data['level']);
        $this->message = $data['message'];
        $this->userId = $data['userId'];
        $this->verb = $data['method'];
        $this->uri = $data['uri'];
        $this->time = strtotime($data['time']);
        $this->domain = $data['domain'];
        $this->parentLogId = $data['parentLogId'];
        try {
            $this->context = json_decode($data['context'], true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception) {
            $this->context = [];
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    public function export(
    ): array
    {
        $response = parent::export();

        $response['logId'] = $this->id;
        $response['parentLogId'] = $this->parentLogId;
        $response['level'] = $this->logLevel->value;
        $response['message'] = $this->message;
        $response['userId'] = $this->userId;
        $response['method'] = $this->verb??'CLI';
        $response['uri'] = $this->uri;
        $response['time'] = date('Y:m:d H:i:s', $this->time);
        $response['domain'] = $this->domain;
        $response['context'] = json_encode($this->context, JSON_THROW_ON_ERROR);

        return $response;
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
        return $this->logLevel;
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
        return $this->id;
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
}