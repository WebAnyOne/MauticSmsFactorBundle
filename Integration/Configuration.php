<?php

declare(strict_types=1);

namespace MauticPlugin\MauticSmsFactorBundle\Integration;

class Configuration
{
    private string $apiToken;

    /**
     * True will simulate sending the text messages rather than sending it for real to the contact
     */
    private bool $simulateSend;

    /**
     * True will always append the <-stop-> tag to the text message whenever contacting the SMSFactor API.
     * This placeholder will be replaced by the API with a generic "stop" message content.
     */
    private bool $alwaysSendStop;

    /**
     * The default country code to use when the phone number does not have a country code prefix.
     */
    private string $defaultCountry;

    public function __construct(
        string $apiToken,
        string $defaultCountry,
        bool $simulateSend = false,
        bool $alwaysSendStop = true
    ) {
        $this->apiToken = $apiToken;
        $this->simulateSend = $simulateSend;
        $this->defaultCountry = $defaultCountry;
        $this->alwaysSendStop = $alwaysSendStop;
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
            'always_send_stop' => true,
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

    public function isAlwaysSendStop(): bool
    {
        return $this->alwaysSendStop;
    }
}
