<?php

declare(strict_types=1);

namespace MauticPlugin\MauticSmsFactorBundle\Integration;

class Configuration
{
    private string $apiToken;
    private bool $simulateSend;

    /**
     * @var string The default country code to use when the phone number does not have a country code prefix.
     */
    private string $defaultCountry;

    public function __construct(
        string $apiToken,
        string $defaultCountry,
        bool $simulateSend = false
    ) {
        $this->apiToken = $apiToken;
        $this->simulateSend = $simulateSend;
        $this->defaultCountry = $defaultCountry;
    }

    /**
     * @internal
     *
     * @return array{
     *   simulate_send: bool,
     *   default_country: string,
     * }
     */
    public static function getDefaults(): array
    {
        return [
            'simulate_send' => false,
            'default_country' => 'FR',
        ];
    }

    public function getApiToken(): string
    {
        return $this->apiToken;
    }

    public function isSimulateSend(): bool
    {
        return $this->simulateSend;
    }

    public function getDefaultCountry(): string
    {
        return $this->defaultCountry;
    }
}
