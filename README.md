# Mautic SMSFactor Bundle

This plugin integrates [SMSFactor](https://www.smsfactor.com/) with Mautic.

## Installation

- require it via composer

```shell
composer require 'webanyone/mautic-smsfactor-bundle:*'
```

## Configuration

1. Open the **plugins** configuration page, click on "Install/Upgrade Plugins" button, and you must saw a new SMSFactor Plugin.
1. Create an API token on [SMSFactor dashboard](https://secure.smsfactor.com/token.html) and paste it in the plugin
configuration.
1. Go to the **Text Messaging Setting** page and select the `SMSFactor` transport.

### Webhook

Add the following `MO` and/or `STOP` webhooks on [SMSfactor dashboard](https://secure.smsfactor.com/webhooks.html):

```text
https://your-mautic-url.com/sms/smsfactor/callback
```

> **Note**:
> The `STOP` webhook at least should be configured to properly mark your unsubscribed contacts in Mautic.
