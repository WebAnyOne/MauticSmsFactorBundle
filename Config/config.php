<?php

declare(strict_types=1);

use MauticPlugin\MauticSmsFactorBundle\Callback\SmsFactorCallback;
use MauticPlugin\MauticSmsFactorBundle\Integration\Configuration;
use MauticPlugin\MauticSmsFactorBundle\Integration\ConfigurationFactory;
use MauticPlugin\MauticSmsFactorBundle\Integration\SmsFactorIntegration;
use MauticPlugin\MauticSmsFactorBundle\Transport\SmsFactorTransport;

return [
    'name' => 'SMS factor',
    'description' => 'Send text messages with SMS factor',
    'version' => '0.0.1',
    'author' => 'elao',
    'services' => [
        'integrations' => [
            'mautic.integration.smsfactor' => [
                'class' => SmsFactorIntegration::class,
                'arguments' => [],
                'tags' => [
                    'mautic.integration',
                    'mautic.basic_integration',
                    'mautic.config_integration',
                ],
            ],
        ],
        'other' => [
            'mautic.sms.smsfactor.configuration_factory' => [
                'class' => ConfigurationFactory::class,
                'arguments' => [
                    'mautic.integrations.helper',
                ],
            ],
            'mautic.sms.smsfactor.configuration' => [
                'class' => Configuration::class,
                'factory' => [
                    '@mautic.sms.smsfactor.configuration_factory',
                    'create',
                ],
            ],
            'mautic.sms.smsfactor.transport' => [
                'class' => SmsFactorTransport::class,
                'arguments' => [
                    'mautic.sms.smsfactor.configuration',
                    'monolog.logger.mautic',
                ],
                'tag' => 'mautic.sms_transport',
                'tagArguments' => [
                    'integrationAlias' => 'SmsFactor',
                ],
                'serviceAliases' => [
                    'sms_api',
                    'mautic.sms.api',
                ],
            ],
            'mautic.sms.smsfactor.callback' => [
                'class' => SmsFactorCallback::class,
                'arguments' => [
                    'mautic.sms.helper.contact',
                ],
                'tag' => 'mautic.sms_callback_handler',
            ],
        ],
    ],

    'menu' => [
        'main' => [
            'items' => [
                'mautic.sms.smses' => [
                    'route' => 'mautic_sms_index',
                    'access' => ['sms:smses:viewown', 'sms:smses:viewother'],
                    'parent' => 'mautic.core.channels',
                    'checks' => [
                        'integration' => [
                            'SmsFactor' => [
                                'enabled' => true,
                            ],
                        ],
                    ],
                    'priority' => 70,
                ],
            ],
        ],
    ],
];
