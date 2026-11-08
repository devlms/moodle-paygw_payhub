<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Strings for paygw_payhub.
 *
 * @package    paygw_payhub
 * @copyright  2026 DevLMS <contact@devlms.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['activate'] = 'Activate (installs version {$a})';
$string['checkout'] = 'Choose how to pay';
$string['code'] = 'Code';
$string['enable'] = 'Enable';
$string['enabled'] = '{$a} is enabled. Set it up in the payment account, then offer it in the hub.';
$string['error:checksum'] = 'The download of {$a} does not match the checksum GitHub publishes for that release, so nothing was installed.';
$string['error:download'] = 'Could not download {$a}.';
$string['error:noneoffered'] = 'Offer at least one gateway to enable the hub.';
$string['error:nooption'] = 'This way to pay is not available for this item.';
$string['error:norelease'] = 'No release with an install file was found on GitHub for {$a}.';
$string['error:notwritable'] = 'The payment gateway folder on the server is not writable, so gateways cannot be installed from here. Install them by hand.';
$string['error:rates'] = 'Not a rate: "{$a}". Write one per line, such as USD EUR 0.92.';
$string['error:signature'] = 'The release of {$a} is not signed with the DevLMS release key, so nothing was installed.';
$string['error:unknowngateway'] = 'The hub does not know a gateway called {$a}.';
$string['error:wrongplugin'] = 'The downloaded file does not contain the {$a} gateway, so nothing was installed.';
$string['event:paymentcompleted'] = 'Payment completed through the hub';
$string['feecurrencies'] = 'Fee currencies for conversion';
$string['feecurrencies_desc'] = 'Currencies a price can be set in even when no gateway charges in them, because the account\'s rates convert them. Example: USD, EUR. A course priced in USD can then be paid through a gateway that charges in another currency.';
$string['gatewaydescription'] = 'Local and international payment at one checkout, each with its own price.';
$string['gatewayname'] = 'Local or international payment';
$string['guide'] = 'Setup guide';
$string['manualinstall'] = 'This server cannot install it from here. <a href="{$a}">Download the release</a>, unzip it into payment/gateway, then visit Site administration > Notifications.';
$string['nooptions'] = 'No way to pay is set up for this item yet. Contact the site administrator.';
$string['noproviders'] = 'No gateway is installed yet. <a href="{$a}">Choose gateways to install</a>.';
$string['offer'] = 'Offer at checkout';
$string['offer_help'] = 'Gateways installed on this site that this payment account offers through the hub. Each gateway\'s keys are set in its own section of this payment account.';
$string['pluginname'] = 'Payment hub';
$string['pluginname_desc'] = 'Local and international payment gateways at one checkout.';
$string['privacy:metadata:paygw_payhub'] = 'Payments the hub started through a gateway.';
$string['privacy:metadata:paygw_payhub:amount'] = 'The amount asked, in the gateway\'s currency.';
$string['privacy:metadata:paygw_payhub:currency'] = 'The currency charged.';
$string['privacy:metadata:paygw_payhub:provider'] = 'The gateway used.';
$string['privacy:metadata:paygw_payhub:status'] = 'Whether the payment is pending or paid.';
$string['privacy:metadata:paygw_payhub:timecreated'] = 'When the payment was started.';
$string['privacy:metadata:paygw_payhub:userid'] = 'The person paying.';
$string['providers'] = 'Gateways';
$string['providersintro'] = 'Gateways the hub can offer. Activate installs a gateway; then set its keys in a <a href="{$a}">payment account</a> and offer it in the hub\'s section of that account.';
$string['rates'] = 'Exchange rates';
$string['rates_help'] = 'Used when a price is in one currency and a gateway charges in another. One rate per line: USD EUR 0.92 means 1 USD costs 0.92 EUR. Converted prices are rounded up.';
$string['region:international'] = 'International payment';
$string['region:local'] = 'Local payment';
$string['state:enabled'] = 'Enabled';
