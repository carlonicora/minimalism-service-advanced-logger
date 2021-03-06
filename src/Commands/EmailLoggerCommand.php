<?php
namespace CarloNicora\Minimalism\Services\AdvancedLogger\Commands;

use CarloNicora\Minimalism\Enums\LogLevel;
use CarloNicora\Minimalism\Interfaces\Encrypter\Interfaces\EncrypterInterface;
use CarloNicora\Minimalism\Interfaces\Mailer\Enums\RecipientType;
use CarloNicora\Minimalism\Interfaces\Mailer\Interfaces\MailerInterface;
use CarloNicora\Minimalism\Interfaces\Mailer\Objects\Email;
use CarloNicora\Minimalism\Interfaces\Mailer\Objects\Recipient;
use CarloNicora\Minimalism\Interfaces\SimpleObjectInterface;
use CarloNicora\Minimalism\Services\AdvancedLogger\Data\Log\DataObjects\Log;
use CarloNicora\Minimalism\Services\AdvancedLogger\Interfaces\LoggerCommandInterface;

class EmailLoggerCommand implements LoggerCommandInterface, SimpleObjectInterface
{
    /** @var Recipient[]  */
    private array $distributionList;

    /** @var Recipient  */
    private Recipient $sender;

    /** @var bool  */
    private bool $isActive=false;

    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly EncrypterInterface $encrypter,
        ?string $MINIMALISM_SERVICE_ADVANCED_LOGGER_SENDER=null,
        ?string $MINIMALISM_SERVICE_ADVANCED_LOGGER_DISTRIBUTION_LIST=null,
        private readonly ?string $MINIMALISM_SERVICE_ADVANCED_LOGGER_URL=null,
    )
    {
        if ($MINIMALISM_SERVICE_ADVANCED_LOGGER_SENDER !== null && $MINIMALISM_SERVICE_ADVANCED_LOGGER_DISTRIBUTION_LIST !== null){
            foreach (explode(';', $MINIMALISM_SERVICE_ADVANCED_LOGGER_DISTRIBUTION_LIST) as $receiver){
                [$receiverEmail, $receiverName] = explode(',', $receiver);

                $this->distributionList[] = new Recipient(
                    emailAddress: $receiverEmail,
                    name: $receiverName,
                    type: RecipientType::To,
                );
            }

            [$senderEmail, $senderName] = explode(',', $MINIMALISM_SERVICE_ADVANCED_LOGGER_SENDER);

            $this->sender = new Recipient(
                emailAddress: $senderEmail,
                name: $senderName,
                type: RecipientType::Sender,
            );

            $this->isActive = true;
        }
    }

    /**
     * @param Log $log
     * @return bool
     */
    public function isLogLevelEnough(
        Log $log,
    ): bool
    {
        return ($this->isActive && $log->getLogLevel()->value >= LogLevel::Emergency->value);
    }

    /**
     * @param Log $log
     * @return Log
     */
    public function writeLog(
        Log $log,
    ): Log
    {
        if ($this->isActive) {
            $link = $this->MINIMALISM_SERVICE_ADVANCED_LOGGER_URL !== null
                ? $this->MINIMALISM_SERVICE_ADVANCED_LOGGER_URL . $this->encrypter->encryptId($log->getId())
                : '';

            $email = new Email(
                sender: $this->sender,
                subject: '[EMERGENCY] ' . $log->getMessage(),
                body: 'Emergency error ' . $log->getId() . PHP_EOL . $link,
            );

            /** @var Recipient $recipient */
            foreach ($this->distributionList as $recipient) {
                $email->addRecipient($recipient);
            }

            $this->mailer->send(
                $email,
            );
        }

        return $log;
    }
}