<?php

declare(strict_types=1);

namespace MauticPlugin\MauticSmsFactorBundle\Form\DataTransformer;

use Mautic\CoreBundle\Form\Type\BooleanType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Transforms between a Boolean and an integer (for {@link BooleanType} and {@link YesNoBooleanTransformer}).
 */
class YesNoBooleanTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!\is_bool($value)) {
            throw new TransformationFailedException('Expected a Boolean.');
        }

        return $value ? 1 : 0;
    }

    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!\is_int($value)) {
            throw new TransformationFailedException('Expected an integer.');
        }

        return $value === 1;
    }
}
