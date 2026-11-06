# kwtSMS for FreeScout

SMS for [FreeScout](https://freescout.net) through [kwtSMS](https://www.kwtsms.com), the SMS gateway for Kuwait and
the Gulf.

## Features

- **Agent alerts:** an SMS to your on-call numbers when a customer starts a conversation.
- **Customer confirmations:** an SMS with the conversation number when a customer with a phone number writes in.
  Off by default.
- Message templates with `{number}`, `{subject}` and `{name}`. English and Arabic.
- Test mode: messages are queued on kwtSMS and never delivered, no credits used.
- The API password is stored encrypted and never shown again in the settings page.
- A failed SMS never stops a conversation; it is written to the FreeScout log.

## Requirements

- FreeScout 1.8 or later on PHP 7.4 or later, with curl and json.
- A kwtSMS account with API access. [Sign up free](https://www.kwtsms.com/signup/).

## Installation

1. Download `kwtsms-freescout-<version>.zip` from the [latest release](https://github.com/kwtsms/kwtsms-freescout/releases/latest).
2. Unzip it into the `Modules/` folder of FreeScout, so you have `Modules/KwtSms/module.json`.
3. In FreeScout open **Manage > Modules** and activate **KwtSms**.

## Setup

1. Open **Manage > Settings > kwtSMS**.
2. Enter the API username and password of your kwtSMS account (not your mobile number) and your Sender ID (default
   `KWT-SMS`).
3. Turn on agent alerts (with the alert numbers), customer confirmations, or both, and save.

Phone numbers go in international format, for example `965XXXXXXXX`. Customer confirmations use the first phone
number in the customer's profile.

## Arabic

Arabic messages are sent as Unicode: one SMS page holds 70 Arabic characters, against 160 English ones. Keep Arabic
templates short to save credits.

## Troubleshooting

Failed messages are logged as `[kwtSMS] new conversation SMS ... failed` with the kwtSMS error code: `ERR003` wrong
API login, `ERR010` zero balance, `ERR008` banned Sender ID, `ERR024` the server IP is not in your kwtSMS API IP
whitelist.

## Development

```sh
php tests/run.php     # unit tests, no FreeScout and no network needed
sh build.sh           # release zip in dist/
```

## Support

Help: https://www.kwtsms.com/support.html | FAQ: https://www.kwtsms.com/faq/ | support@kwtsms.com

## Security

See [SECURITY.md](SECURITY.md).

## License

MIT, (c) kwtSMS
