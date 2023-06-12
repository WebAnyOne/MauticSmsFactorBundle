<?php

declare(strict_types=1);

namespace MauticPlugin\MauticSmsFactorBundle\Integration;

use Mautic\IntegrationsBundle\Integration\BasicIntegration;
use Mautic\IntegrationsBundle\Integration\DefaultConfigFormTrait;
use Mautic\IntegrationsBundle\Integration\Interfaces\BasicInterface;
use Mautic\IntegrationsBundle\Integration\Interfaces\ConfigFormAuthInterface;
use Mautic\IntegrationsBundle\Integration\Interfaces\ConfigFormFeatureSettingsInterface;
use Mautic\IntegrationsBundle\Integration\Interfaces\ConfigFormInterface;
use MauticPlugin\MauticSmsFactorBundle\Form\Type\AuthConfigFormType;
use MauticPlugin\MauticSmsFactorBundle\Form\Type\FeatureConfigFormType;

class SmsFactorIntegration extends BasicIntegration implements BasicInterface, ConfigFormInterface, ConfigFormAuthInterface, ConfigFormFeatureSettingsInterface
{
    use DefaultConfigFormTrait;

    public const NAME = 'SMSFactor';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getDisplayName(): string
    {
        return 'SMSFactor';
    }

    public function getIcon(): string
    {
        return 'plugins/MauticSmsFactorBundle/Assets/img/smsfactor.png';
    }

    public function getAuthConfigFormName(): string
    {
        return AuthConfigFormType::class;
    }

    public function getFeatureSettingsConfigFormName(): string
    {
        return FeatureConfigFormType::class;
    }
}
