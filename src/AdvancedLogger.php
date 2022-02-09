<?php
namespace CarloNicora\Minimalism\Services\AdvancedLogger;

use CarloNicora\Minimalism\Abstracts\AbstractService;
use CarloNicora\Minimalism\Factories\ServiceFactory;
use CarloNicora\Minimalism\Factories\TimerFactory;
use CarloNicora\Minimalism\Interfaces\Security\Interfaces\SecurityInterface;
use CarloNicora\Minimalism\Objects\ModelParameters;
use CarloNicora\Minimalism\Services\AdvancedLogger\Commands\EmailLoggerCommand;
use CarloNicora\Minimalism\Services\AdvancedLogger\Commands\MySqlLoggerCommand;
use CarloNicora\Minimalism\Services\AdvancedLogger\Commands\SlackLoggerCommand;
use CarloNicora\Minimalism\Services\AdvancedLogger\Interfaces\LoggerCommandInterface;
use CarloNicora\Minimalism\Enums\LogLevel;
use CarloNicora\Minimalism\Interfaces\LoggerInterface;
use CarloNicora\Minimalism\Services\AdvancedLogger\Traits\AdditionalInformationTrait;
use CarloNicora\Minimalism\Services\AdvancedLogger\Traits\LoggerFunctionsTraits;
use CarloNicora\Minimalism\Services\Geolocator\Geolocator;
use CarloNicora\Minimalism\Services\Path;
use Exception;
use Throwable;

class AdvancedLogger extends AbstractService implements LoggerInterface
{
    use LoggerFunctionsTraits;
    use AdditionalInformationTrait;

    /** @var LoggerCommandInterface[]  */
    private array $loggerCommands;

    /** @var MySqlLoggerCommand|null */
    private ?MySqlLoggerCommand $loggerCommand=null;

    /**
     * @return string|null
     */
    public static function getBaseInterface(): ?string
    {
        return LoggerInterface::class;
    }

    /**
     * @param Path $path
     * @param int $MINIMALISM_LOG_LEVEL
     * @noinspection PhpUnusedParameterInspection
     */
    public function __construct(
        Path $path,
        private int $MINIMALISM_LOG_LEVEL=200,
    )
    {
        $this->logLevel = LogLevel::from($this->MINIMALISM_LOG_LEVEL);
    }

    /**
     * @param ServiceFactory $services
     * @return void
     * @throws Exception
     */
    public function postIntialise(
        ServiceFactory $services,
    ): void
    {
        $this->geolocator = $services->create(Geolocator::class);
        $this->authorisation = $services->create(SecurityInterface::class);

        $this->logs = [];
        $this->loggerCommand = $this->objectFactory->create(MySqlLoggerCommand::class);

        $this->loggerCommands = [];

        $parameters = new ModelParameters();
        $parameters->addNamedParameter('MINIMALISM_LOG_LEVEL', $this->MINIMALISM_LOG_LEVEL);
        foreach ($_ENV as $envParams => $envValue){
            if (str_starts_with($envParams, 'MINIMALISM_SERVICE_ADVANCED_LOGGER_')){
                $parameters->addNamedParameter($envParams, $envValue);
            }
        }

        foreach ([EmailLoggerCommand::class, SlackLoggerCommand::class] as $commandInterface) {
            try {
                $this->loggerCommands[] = $this->objectFactory->create(className: $commandInterface, parameters: $parameters);
            } catch (Exception|Throwable) {
            }
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function destroy(
    ): void
    {
        $this->flush();
        $this->loggerCommand = null;
        $this->logs = [];
        $this->loggerCommands = [];

        parent::destroy();
    }

    /**
     * @return void
     * @throws Exception
     */
    public function flush(
    ): void
    {
        if ($this->objectFactory !== null) {
            $this->setupAdditionalInformation();

            $parentLogId = null;

            foreach ($this->logs ?? [] as $log) {

                $log->addContext(['releaseDuration' => TimerFactory::elapse()]);

                if ($log->getLogLevel()->value >= $this->MINIMALISM_LOG_LEVEL) {
                    $this->addAdditionalInformation(
                        log: $log,
                        parentLogId: $parentLogId,
                    );

                    $log = $this->loggerCommand->writeLog($log);

                    if ($parentLogId === null) {
                        $parentLogId = $log->getId();
                    }

                    foreach ($this->loggerCommands as $loggerCommand){
                        if ($loggerCommand->isLogLevelEnough($log)){
                            $log = $loggerCommand->writeLog($log);
                        }
                    }
                }
            }
        }
    }
}