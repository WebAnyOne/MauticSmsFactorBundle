<?php

declare(strict_types=1);

namespace MauticPlugin\MauticSmsFactorBundle\Form\Type;

use Mautic\CoreBundle\Form\Type\BooleanType;
use MauticPlugin\MauticSmsFactorBundle\Form\DataTransformer\YesNoBooleanTransformer;
use MauticPlugin\MauticSmsFactorBundle\Integration\Configuration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class FeatureConfigFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Configure defaults for the feature settings if not already set:
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $defaults = Configuration::getDefaults();
            $featureSettings = ($event->getData() ?? []) + $defaults;

            $event->setData($featureSettings);
        });

        $builder
            ->add('simulate_send', BooleanType::class, [
                'label' => 'mautic.smsfactor.config.simulate_send',
                'attr' => [
                    'tooltip' => 'mautic.smsfactor.config.simulate_send.tooltip',
                ],
            ])
            ->add('default_country', CountryType::class, [
                'label' => 'mautic.smsfactor.config.default_country',
                'attr' => [
                    'tooltip' => 'mautic.smsfactor.config.default_country.tooltip',
                ],
            ])
        ;

        $builder->get('simulate_send')->addModelTransformer(new YesNoBooleanTransformer());
    }
}
