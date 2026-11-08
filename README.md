# Payment hub for Moodle: local and international payment at one checkout

[![CI](https://github.com/devlms/moodle-paygw_payhub/actions/workflows/ci.yml/badge.svg)](https://github.com/devlms/moodle-paygw_payhub/actions/workflows/ci.yml)
[![Maturity](https://img.shields.io/badge/maturity-alpha-orange)](version.php)

A Moodle payment gateway that offers several payment gateways at one checkout. Learners choose how to pay, and each option can have its own price in its own currency, converted with rates you set.

Gateways are separate plugins. Install the ones you need on their own, or activate them from the hub's Gateways page.

| Feature | Status |
| --- | --- |
| Offers every installed gateway that supports the hub, grouped by local and international payment | Done |
| One price per currency: the fee's own, or converted with the account's rates (rounded up) | Done |
| Gateways page: Activate installs a gateway's newest release with Moodle's own installer, after checking its checksum and signature | Done |
| Each gateway keeps its own keys in the payment account; the hub reads them, nothing is duplicated | Done |
| Payments recorded in the currency actually charged, each delivered once | Done |
| English, French and Arabic | Done |
| Supported gateways | [Chargily Pay](https://github.com/devlms/moodle-paygw_chargily); more are added as they are released |

## Two ways to install

- **One gateway**: install a gateway plugin such as [Chargily Pay](https://github.com/devlms/moodle-paygw_chargily) on its own. It works without the hub.
- **All in one**: install the hub, open Site administration > Plugins > Payment gateways > Payment hub > Gateways, and activate the ones you need. If the server's code folder is not writable, the page links to the release to install by hand.

## Set up

1. Enable the hub (and the gateways you activated) in Site administration > Plugins > Payment gateways.
2. In a payment account, set each gateway's keys in its own section. Leave the gateway itself disabled there if it should only be offered through the hub.
3. In the hub's section of the same account, tick the gateways to offer and, if a gateway charges in another currency than the course price, add rates, one per line: `USD EUR 0.92` means 1 USD costs 0.92 EUR.
4. Price the course (for example with Enrolment on payment) and choose that account.

## For gateway developers

A gateway joins the hub by shipping one class, `\paygw_<name>\payhub\provider`, implementing `\paygw_payhub\provider`: its region, currencies, the methods an account turned on, and `start()`, which returns the provider's checkout URL. When the provider confirms the payment, the gateway calls `\paygw_payhub\hub::complete()` with the hub's reference and the amount and currency charged. See the Chargily gateway for a complete example.

## Licence

GPL v3 or later. Built by [DevLMS](https://devlms.com), who develop Moodle plugins. Need a gateway that is not supported yet? [Get a quote](https://devlms.com/#quote).
