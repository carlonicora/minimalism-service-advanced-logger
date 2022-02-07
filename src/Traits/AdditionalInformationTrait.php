<?php
namespace CarloNicora\Minimalism\Services\AdvancedLogger\Traits;

use CarloNicora\Minimalism\Interfaces\Security\Interfaces\SecurityInterface;
use CarloNicora\Minimalism\Services\AdvancedLogger\DataObjects\Log;
use CarloNicora\Minimalism\Services\Geolocator\Geolocator;
use Exception;
use Throwable;

trait AdditionalInformationTrait
{
    /** @var Geolocator  */
    private Geolocator $geolocator;

    /** @var SecurityInterface  */
    protected SecurityInterface $authorisation;

    /** @var array  */
    private array $ipLocation = [];

    /** @var string  */
    private string $domain;

    /** @var int|null  */
    private ?int $userId=null;

    /**
     * @return void
     */
    protected function setupAdditionalInformation(
    ): void
    {
        $ip = null;

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        if ($ip !== null){
            $this->ipLocation = $this->getIpLocation($ip);
        }

        try {
            $app = $this->authorisation->getApp();
            $this->domain = $app?->getName() . '(' . $app?->getId() . ')';
            $this->userId = $this->authorisation->getUserId();
        } catch (Exception|Throwable) {
            $this->domain = '';
            $this->userId = null;
        }
    }

    /**
     * @param Log $log
     * @param int|null $parentLogId
     * @return void
     */
    protected function addAdditionalInformation(
        Log $log,
        ?int $parentLogId,
    ): void
    {
        if ($parentLogId !== null) {
            $log->setParentLogId($parentLogId);
        }

        $log->addContext($this->ipLocation);
        $log->setDomain(
            domain: $this->domain,
        );

        if ($this->userId !== null) {
            $log->setUserId($this->userId);
        }
    }

    /**
     * @param string $ip
     * @return array
     */
    private function getIpLocation(
        string $ip,
    ): array
    {
        $countryCode = null;
        $cityName = null;
        $latitude = 0.0;
        $longitude = 0.0;

        $this->geolocator->lookupIP(
            ip: $ip,
            countryCode: $countryCode,
            cityName: $cityName,
            latitude: $latitude,
            longitude: $longitude,
        );

        return [
            'source' => [
                'ip' => $ip,
                'country' => $countryCode,
                'city' => $cityName
            ],
        ];
    }
}