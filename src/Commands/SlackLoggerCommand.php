<?php
namespace CarloNicora\Minimalism\Services\AdvancedLogger\Commands;

use CarloNicora\Minimalism\Enums\LogLevel;
use CarloNicora\Minimalism\Interfaces\Encrypter\Interfaces\EncrypterInterface;
use CarloNicora\Minimalism\Services\Slack\Objects\Elements\SlackMessageElementText;
use CarloNicora\Minimalism\Services\Slack\Objects\Parts\SlackMessagePartHeader;
use CarloNicora\Minimalism\Services\Slack\Objects\SlackMessage;
use CarloNicora\Minimalism\Services\Slack\Slack;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use CarloNicora\Minimalism\Services\AdvancedLogger\Data\Log;
use CarloNicora\Minimalism\Services\AdvancedLogger\Interfaces\LoggerCommandInterface;

class SlackLoggerCommand implements LoggerCommandInterface
{
    public function __construct(
        private Slack $slack,
        private EncrypterInterface $encrypter,
        private string $MINIMALISM_SERVICE_ADVANCED_LOGGER_SLACK,
        private ?string $MINIMALISM_SERVICE_ADVANCED_LOGGER_URL=null,
    )
    {
    }

    /**
     * @param Log $log
     * @return Log
     * @throws Exception|GuzzleException
     */
    public function writeLog(
        Log $log,
    ): Log
    {
        $link = $this->MINIMALISM_SERVICE_ADVANCED_LOGGER_URL !== null
            ? $this->MINIMALISM_SERVICE_ADVANCED_LOGGER_URL . $this->encrypter->encryptId($log->getId())
            : '';

        $message = new SlackMessage();
        $message->addPart(new SlackMessagePartHeader('Minimalism ' . $log->getLogLevel()->name));
        $message->addPart(new SlackMessageElementText('Error id:' . $log->getId() . PHP_EOL . $link));

        $this->slack->sendSlackMessage(
            message: $message,
            channel: $this->MINIMALISM_SERVICE_ADVANCED_LOGGER_SLACK,
        );

        return $log;
    }

    /**
     * @param Log $log
     * @return bool
     */
    public function isLogLevelEnough(
        Log $log,
    ): bool
    {
        return $log->getLogLevel()->value >= LogLevel::Emergency->value;
    }
}