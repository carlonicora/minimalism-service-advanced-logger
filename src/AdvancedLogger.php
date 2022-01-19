<?php
namespace CarloNicora\Minimalism\Services\AdvancedLogger;

use CarloNicora\Minimalism\Abstracts\AbstractService;
use CarloNicora\Minimalism\Objects\ModelParameters;
use CarloNicora\Minimalism\Services\AdvancedLogger\Commands\EmailLoggerCommand;
use CarloNicora\Minimalism\Services\AdvancedLogger\Commands\MySqlLoggerCommand;
use CarloNicora\Minimalism\Services\AdvancedLogger\Commands\SlackLoggerCommand;
use CarloNicora\Minimalism\Services\AdvancedLogger\Interfaces\LoggerCommandInterface;
use CarloNicora\Minimalism\Enums\LogLevel;
use CarloNicora\Minimalism\Interfaces\LoggerInterface;
use CarloNicora\Minimalism\Services\AdvancedLogger\Traits\AdditionalInformationTrait;
use CarloNicora\Minimalism\Services\AdvancedLogger\Traits\LoggerFunctionsTraits;
use CarloNicora\Minimalism\Services\Auth\Auth;
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
     * @param Path $path
     * @param int $MINIMALISM_LOG_LEVEL
     * @param Geolocator|null $geolocator
     * @param Auth|null $auth
     * @noinspection PhpUnusedParameterInspection
     */
    public function __construct(
        Path $path,
        private int $MINIMALISM_LOG_LEVEL=200,
        ?Geolocator $geolocator=null,
        ?Auth $auth=null,
    )
    {
        $this->logLevel = LogLevel::from($this->MINIMALISM_LOG_LEVEL);

        $this->geolocator = $geolocator;
        $this->auth = $auth;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function initialise(
    ): void
    {
        parent::initialise();
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