<?php

declare(strict_types=1);

namespace MauticPlugin\MauticSmsFactorBundle\Integration;

use Mautic\IntegrationsBundle\Helper\IntegrationsHelper;
use Mautic\PluginBundle\Entity\Integration;

/**
 * Instantiates the {@link Configuration} for the integration from the generic {@link Integration} object.
 */
class ConfigurationFactory
{
    private IntegrationsHelper $integrationHelper;

    public function __construct(IntegrationsHelper $integrationsHelper)
    {
        $this->integrationHelper = $integrationsHelper;
    }

    public function create(): Configuration
    {
        $integration = $this->integrationHelper->getIntegration('SMSFactor');
        $config = $this->integrationHelper->getIntegrationConfiguration($integration);

        ['api_token' => $apiToken] = $config->getApiKeys();
        [
            'simulate_send' => $simulateSend,
            'always_send_stop' => $alwaysSendStop,
            'default_country' => $defaultCountry,
        ] = ($config->getFeatureSettings()['integration'] ?? []) + Configuration::getDefaults();

        return new Configuration($apiToken, $defaultCountry, $simulateSend, $alwaysSendStop);
    }
}
