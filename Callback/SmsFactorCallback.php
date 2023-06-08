<?php

declare(strict_types=1);

namespace MauticPlugin\MauticSmsFactorBundle\Callback;

use Doctrine\Common\Collections\ArrayCollection;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\SmsBundle\Callback\CallbackInterface;
use Mautic\SmsBundle\Helper\ContactHelper;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Receive text messages from a MO webhook setup on SmsFactor.
 *
 * @see https://dev.smsfactor.com/en/api/sms/webhook/replies
 * @see https://dev.smsfactor.com/en/api/sms/webhook/stops
 */
class SmsFactorCallback implements CallbackInterface
{
    private ContactHelper $contactHelper;

    public function __construct(ContactHelper $contactHelper)
    {
        $this->contactHelper = $contactHelper;
    }

    public function getTransportName()
    {
        return 'smsfactor';
    }

    /**
     * @return ArrayCollection<Lead>
     * @phpstan-ignore-next-line
     */
    public function getContacts(Request $request)
    {
        $this->validateRequest($request->request);

        $number = $request->get('from');

        // SmsFactor sends the number without the leading + sign.
        if (!str_starts_with($number, '+')) {
            $number = "+$number";
        }

        return $this->contactHelper->findContactsByNumber($number);
    }

    public function getMessage(Request $request)
    {
        $this->validateRequest($request->request);

        return trim($request->get('message'));
    }

    private function validateRequest(ParameterBag $request): void
    {
        $number = $request->get('from');
        if (empty($number)) {
            throw new BadRequestHttpException('Missing "from" parameter');
        }

        $message = trim($request->get('message'));
        if (empty($message)) {
            throw new BadRequestHttpException('Missing "message" parameter');
        }
    }
}
