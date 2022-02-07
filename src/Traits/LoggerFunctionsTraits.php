<?php
namespace CarloNicora\Minimalism\Services\AdvancedLogger\Traits;

use CarloNicora\Minimalism\Enums\LogLevel;
use CarloNicora\Minimalism\Services\AdvancedLogger\DataObjects\Log;

trait LoggerFunctionsTraits
{
    /** @var Log[]  */
    protected array $logs=[];

    /** @var LogLevel  */
    protected LogLevel $logLevel;

    /**
     * @return LogLevel
     */
    public function getLogLevel(
    ): LogLevel
    {
        return $this->logLevel;
    }

    /**
     * @param string $name
     * @param string|int $value
     */
    public function addExtraInformation(
        string $name,
        string|int $value,
    ): void
    {
    }

    /**
     * @param string $message
     * @param string|null $domain
     * @param array $context
     */
    public function debug(
        string $message,
        ?string $domain=null,
        array $context = []
    ): void
    {
        $this->logs[] = new Log(
            objectFactory: $this->objectFactory,
            logLevel: LogLevel::Debug,
            message: $message,
            domain: $domain,
            context: $context,
        );
    }

    /**
     * @param string $message
     * @param string|null $domain
     * @param array $context
     */
    public function info(
        string $message,
        ?string $domain=null,
        array $context = []
    ): void
    {
        $this->logs[] = new Log(
            objectFactory: $this->objectFactory,
            logLevel: LogLevel::Info,
            message: $message,
            domain: $domain,
            context: $context,
        );
    }

    /**
     * @param string $message
     * @param string|null $domain
     * @param array $context
     */
    public function notice(
        string $message,
        ?string $domain=null,
        array $context = []
    ): void
    {
        $this->logs[] = new Log(
            objectFactory: $this->objectFactory,
            logLevel: LogLevel::Notice,
            message: $message,
            domain: $domain,
            context: $context,
        );
    }

    /**
     * @param string $message
     * @param string|null $domain
     * @param array $context
     */
    public function warning(
        string $message,
        ?string $domain=null,
        array $context = []
    ): void
    {
        $this->logs[] = new Log(
            objectFactory: $this->objectFactory,
            logLevel: LogLevel::Warning,
            message: $message,
            domain: $domain,
            context: $context,
        );
    }

    /**
     * @param string $message
     * @param string|null $domain
     * @param array $context
     */
    public function error(
        string $message,
        ?string $domain=null,
        array $context = []
    ): void
    {
        $this->logs[] = new Log(
            objectFactory: $this->objectFactory,
            logLevel: LogLevel::Error,
            message: $message,
            domain: $domain,
            context: $context,
        );
    }

    /**
     * @param string $message
     * @param string|null $domain
     * @param array $context
     */
    public function critical(
        string $message,
        ?string $domain=null,
        array $context = []
    ): void
    {
        $this->logs[] = new Log(
            objectFactory: $this->objectFactory,
            logLevel: LogLevel::Critical,
            message: $message,
            domain: $domain,
            context: $context,
        );
    }

    /**
     * @param string $message
     * @param string|null $domain
     * @param array $context
     */
    public function alert(
        string $message,
        ?string $domain=null,
        array $context = []
    ): void
    {
        $this->logs[] = new Log(
            objectFactory: $this->objectFactory,
            logLevel: LogLevel::Alert,
            message: $message,
            domain: $domain,
            context: $context,
        );
    }

    /**
     * @param string $message
     * @param string|null $domain
     * @param array $context
     */
    public function emergency(
        string $message,
        ?string $domain=null,
        array $context = []
    ): void
    {
        $this->logs[] = new Log(
            objectFactory: $this->objectFactory,
            logLevel: LogLevel::Emergency,
            message: $message,
            domain: $domain,
            context: $context,
        );
    }
}