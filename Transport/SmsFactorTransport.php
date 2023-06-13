<?php

declare(strict_types=1);

namespace MauticPlugin\MauticSmsFactorBundle\Transport;

use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\SmsBundle\Entity\Stat;
use Mautic\SmsBundle\Integration\Twilio\TwilioTransport;
use Mautic\SmsBundle\Sms\TransportChain;
use Mautic\SmsBundle\Sms\TransportInterface;
use MauticPlugin\MauticSmsFactorBundle\Integration\Configuration;
use Psr\Log\LoggerInterface;
use SMSFactor\Error\Base;
use SMSFactor\Message;
use SMSFactor\SMSFactor;

/**
 * Send SMS messages using the SMSFactor API.
 *
 * @see https://dev.smsfactor.com/en/api/sms/send/send-single
 */
class SmsFactorTransport implements TransportInterface
{
    private Configuration $configuration;
    private LoggerInterface $logger;

    public function __construct(
        Configuration $configuration,
        LoggerInterface $logger
    ) {
        $this->configuration = $configuration;
        $this->logger = $logger;
    }

    /**
     * @param Stat|null $stat This argument is not part of the interface, but is passed by {@link TransportChain::sendSms}
     *
     * @return bool|string
     */
    public function sendSms(Lead $lead, $content, ?Stat $stat = null)
    {
        $this->configureClient();

        $number = $lead->getLeadPhoneNumber();

        if (null === $number) {
            return false;
        }

        $sanitizedNumber = $this->sanitizeNumber($number);

        $gsmSmsId = null;
        // If available, provide the Mautic SMS object ID as an identifier for the SMSFactor message
        if ($stat !== null) {
            $gsmSmsId = $stat->getSms()->getId();
        }

        try {
            $response = Message::send([
                'to' => $sanitizedNumber,
                'text' => $this->getTextMessageContent($content),
                'gsmsmsid' => $gsmSmsId,
            ], $this->configuration->isSimulateSend());

            /** @var int $statusCode */
            $statusCode = $response->getCode();

            if (($response->status ?? null) !== 1 || $statusCode !== 200) {
                $this->logger->warning('Unable to send the SMS: {response_details}', [
                    'response_details' => $response->details ?? 'Unknown details',
                    'response_message' => $response->message ?? 'Unknown message',
                ]);

                return 'Unable to send the SMS';
            }

            if (($response->invalid ?? null) !== 0) {
                $this->logger->warning($message = 'Unable to send the SMS: Invalid phone number');

                return $message;
            }

            if (($response->sent ?? null) !== 1) {
                $this->logger->warning($message = 'Unable to send the SMS: Unknown error');

                return $message;
            }

            return true;
        } catch (Base $smsFactorError) {
            $this->logger->error('Unexpected error while trying to contact SMSFactor API: {exception_message}', [
                'exception' => $smsFactorError,
                'exception_message' => $smsFactorError->getMessage(),
            ]);

            return $smsFactorError->getMessage();
        } catch (\Throwable $exception) {
            $this->logger->error('Unexpected error while trying to contact SMSFactor API: {exception_message}', [
                'exception' => $exception,
                'exception_message' => $exception->getMessage(),
            ]);

            return 'An unexpected error occurred while trying to contact SMSFactor API. Please check your logs for more details.';
        }
    }

    /**
     * Reuses same logic as {@link TwilioTransport::sanitizeNumber()},
     * but we account for a default country code for prefix if none set in the provided number.
     *
     * @see https://dev.smsfactor.com/en/api/sms/getting-started#number-format
     */
    private function sanitizeNumber(string $number): string
    {
        $util = PhoneNumberUtil::getInstance();
        /** @var PhoneNumber $parsed */
        $parsed = $util->parse($number, $this->configuration->getDefaultCountry());

        return $util->format($parsed, PhoneNumberFormat::E164);
    }

    private function configureClient(): void
    {
        $apiToken = $this->configuration->getApiToken();

        if (null !== SMSFactor::$apiToken) {
            // Already configured
            return;
        }

        SMSFactor::setApiToken($apiToken);
    }

    private function getTextMessageContent(string $content): string
    {
        // If enabled, always append the STOP message placeholder before sending ot the API.
        // This placeholder will be replaced by the SMSFactor API with the appropriate STOP message.
        if ($this->configuration->isAlwaysSendStop()) {
            $content .= "\n<-stop->";
        }

        return $content;
    }
}
