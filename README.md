# Payment hub for Moodle: local and international payment at one checkout

[![CI](https://github.com/devlms/moodle-paygw_payhub/actions/workflows/ci.yml/badge.svg)](https://github.com/devlms/moodle-paygw_payhub/actions/workflows/ci.yml)
[![Maturity](https://img.shields.io/badge/maturity-alpha-orange)](version.php)

Most platforms in North Africa and the Middle East need two ways to take money: a local one (in Algeria, EDAHABIA and CIB cards in dinars) for local learners, and an international one (cards, wallets) for everyone else. In Moodle each gateway is installed and configured on its own, and a course fee has a single currency, so a course priced in dinars can never be paid by card in dollars.

The hub is one gateway in Moodle's payment window. At checkout the learner sees "Local payment" and "International payment", each with its own price, and pays with the gateway they choose.

| Feature | Status |
| --- | --- |
| Offers every installed gateway that supports the hub, grouped local and international | Done |
| One price per currency: the fee's own, or converted with the account's rates (rounded up) | Done |
| Gateways page: Activate installs a gateway's pinned release (SHA-256 checked) with Moodle's own installer | Done |
| Each gateway keeps its own keys in the payment account; the hub reads them, nothing is duplicated | Done |
| Payments recorded in the currency actually charged, each delivered once | Done |
| English, French and Arabic | Done |
| Gateways: Chargily Pay (Algeria, EDAHABIA and CIB) | Done |
| More gateways (SofizPay, Moyasar, Tap, international cards) | Planned |

## Two ways to install

- **One gateway**: install a gateway plugin such as [Chargily Pay](https://github.com/devlms/moodle-paygw_chargily) on its own. It works without the hub.
- **All in one**: install the hub, open Site administration > Plugins > Payment gateways > Payment hub > Gateways, and Activate the ones you need. If the server's code folder is not writable, the page gives the download link and manual steps instead.

## Set up

1. Enable the hub (and the gateways you activated) in Site administration > Plugins > Payment gateways.
2. In a payment account, set each gateway's keys in its own section. Leave the gateway itself disabled there if it should only be offered through the hub.
3. In the hub's section of the same account, tick the gateways to offer and, if the course price is in another currency, add rates, one per line: `USD DZD 135.5`.
4. Price the course (for example with Enrolment on payment) and choose that account.

## For gateway developers

A gateway joins the hub by shipping one class, `\paygw_<name>\payhub\provider`, implementing `\paygw_payhub\provider`: its region, currencies, the methods an account turned on, and `start()`, which returns the provider's checkout URL. When the provider confirms the payment, the gateway calls `\paygw_payhub\hub::complete()` with the hub's reference and the amount and currency charged. See the Chargily gateway for a complete example.

## Licence

GPL v3 or later. Built by [DevLMS](https://devlms.com). Need a gateway for your country? [Get a quote](https://devlms.com/#quote).
