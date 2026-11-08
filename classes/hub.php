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

namespace paygw_payhub;

use core_payment\helper;
use paygw_payhub\local\pricing;

/**
 * Starts payments through a provider, and takes them back when the provider has confirmed them.
 *
 * @package    paygw_payhub
 * @copyright  2026 DevLMS <contact@devlms.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class hub {
    /**
     * The options for an item: each usable provider with its price in its own currency.
     *
     * @param string $component
     * @param string $paymentarea
     * @param int $itemid
     * @return array[] Each: provider, region, currency, amount, methods (key => label). Local first.
     */
    public static function options(string $component, string $paymentarea, int $itemid): array {
        $payable = helper::get_payable($component, $paymentarea, $itemid);
        $account = new \core_payment\account($payable->get_account_id());
        $hubconfig = helper::get_gateway_configuration($component, $paymentarea, $itemid, 'payhub');
        $rates = pricing::parse_rates((string) ($hubconfig['rates'] ?? ''));
        $fee = helper::get_rounded_cost($payable->get_amount(), $payable->get_currency(), helper::get_gateway_surcharge('payhub'));
        $options = [];
        foreach (registry::usable($account, $hubconfig) as $name => $p) {
            foreach ($p['class']::currencies() as $currency) {
                $amount = pricing::convert($fee, $payable->get_currency(), $currency, $rates);
                if ($amount === null) {
                    continue;
                }
                $options[] = ['provider' => $name, 'region' => $p['class']::region(), 'currency' => $currency,
                    'amount' => $amount, 'methods' => $p['methods']];
                break;
            }
        }
        $key = fn($o) => [$o['region'] !== provider::LOCAL, $o['provider']];
        usort($options, fn($a, $b) => $key($a) <=> $key($b));
        return $options;
    }

    /**
     * Start a payment with one provider and method: record it and return the provider's URL.
     *
     * @param string $component
     * @param string $paymentarea
     * @param int $itemid
     * @param int $userid
     * @param string $provider
     * @param string $method
     * @param string $description
     * @return string
     */
    public static function start(
        string $component,
        string $paymentarea,
        int $itemid,
        int $userid,
        string $provider,
        string $method,
        string $description
    ): string {
        global $DB;
        $option = null;
        foreach (self::options($component, $paymentarea, $itemid) as $o) {
            if ($o['provider'] === $provider && isset($o['methods'][$method])) {
                $option = $o;
            }
        }
        if (!$option) {
            throw new \moodle_exception('error:nooption', 'paygw_payhub');
        }
        $payable = helper::get_payable($component, $paymentarea, $itemid);
        $now = time();
        $row = (object) [
            'reference' => random_string(32),
            'accountid' => $payable->get_account_id(),
            'component' => $component,
            'paymentarea' => $paymentarea,
            'itemid' => $itemid,
            'userid' => $userid,
            'provider' => $provider,
            'method' => $method,
            'amount' => $option['amount'],
            'currency' => $option['currency'],
            'feeamount' => $payable->get_amount(),
            'feecurrency' => $payable->get_currency(),
            'status' => 'pending',
            'timecreated' => $now,
            'timemodified' => $now,
        ];
        $row->id = $DB->insert_record('paygw_payhub', $row);
        $class = registry::installed()[$provider];
        return $class::start(new request(
            $component,
            $paymentarea,
            $itemid,
            $userid,
            $method,
            (float) $option['amount'],
            $option['currency'],
            $description,
            $row->reference
        ));
    }

    /**
     * Called by a gateway once the provider has confirmed the payment: save it in the currency charged, deliver the
     * order. Safe to call twice: the second call returns the same payment.
     *
     * @param string $reference
     * @param string $gateway The gateway that took the payment.
     * @param float $amount Amount charged.
     * @param string $currency Currency charged.
     * @return int|null core payments id, or null when the reference or the amount does not match.
     */
    public static function complete(string $reference, string $gateway, float $amount, string $currency): ?int {
        global $DB;
        $row = $DB->get_record('paygw_payhub', ['reference' => $reference]);
        if (!$row || $row->provider !== $gateway || $row->currency !== $currency || $amount + 0.0001 < (float) $row->amount) {
            return null;
        }
        if ($row->status === 'paid') {
            return (int) $row->paymentid;
        }
        $paymentid = helper::save_payment(
            (int) $row->accountid,
            $row->component,
            $row->paymentarea,
            (int) $row->itemid,
            (int) $row->userid,
            $amount,
            $currency,
            $gateway
        );
        helper::deliver_order($row->component, $row->paymentarea, (int) $row->itemid, $paymentid, (int) $row->userid);
        $DB->update_record('paygw_payhub', (object) ['id' => $row->id, 'status' => 'paid', 'paymentid' => $paymentid,
            'timemodified' => time()]);
        return $paymentid;
    }

    /**
     * Where to send the payer after a hub payment.
     *
     * @param string $reference
     * @return \moodle_url|null
     */
    public static function success_url(string $reference): ?\moodle_url {
        global $DB;
        $row = $DB->get_record('paygw_payhub', ['reference' => $reference]);
        return $row ? helper::get_success_url($row->component, $row->paymentarea, (int) $row->itemid) : null;
    }
}
