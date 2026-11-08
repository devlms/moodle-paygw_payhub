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

use paygw_payhub\local\pricing;

/**
 * The payment hub: local and international gateways at one checkout, each with its own price.
 *
 * @package    paygw_payhub
 * @copyright  2026 DevLMS <contact@devlms.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gateway extends \core_payment\gateway {
    /**
     * Currencies a fee can be in: those the installed providers charge in, plus the ones the site lists for
     * conversion (a course priced in USD, paid in DZD at the account's rate).
     *
     * @return string[]
     */
    public static function get_supported_currencies(): array {
        $currencies = [];
        foreach (registry::installed() as $class) {
            $currencies = array_merge($currencies, $class::currencies());
        }
        $extra = preg_split('/[\s,]+/', strtoupper((string) get_config('paygw_payhub', 'feecurrencies')), -1, PREG_SPLIT_NO_EMPTY);
        $currencies = array_merge($currencies, array_filter($extra, fn($c) => preg_match('/^[A-Z]{3}$/', $c)));
        return array_values(array_unique($currencies));
    }

    /**
     * Account settings: which installed gateways to offer, and the rates between currencies.
     *
     * @param \core_payment\form\account_gateway $form
     */
    public static function add_configuration_to_gateway_form(\core_payment\form\account_gateway $form): void {
        $mform = $form->get_mform();
        $installed = registry::installed();
        if (!$installed) {
            $mform->addElement('static', 'noproviders', '', get_string(
                'noproviders',
                'paygw_payhub',
                (new \moodle_url('/payment/gateway/payhub/providers.php'))->out(false)
            ));
        }
        $first = true;
        foreach ($installed as $name => $class) {
            $label = get_string('pluginname', 'paygw_' . $name) . ' (' . get_string('region:' . $class::region(), 'paygw_payhub') .
                ', ' . implode(', ', $class::currencies()) . ')';
            $mform->addElement('advcheckbox', 'use_' . $name, $first ? get_string('offer', 'paygw_payhub') : '', $label);
            $first = false;
        }
        if ($installed) {
            $mform->addHelpButton('use_' . array_key_first($installed), 'offer', 'paygw_payhub');
        }
        $mform->addElement('textarea', 'rates', get_string('rates', 'paygw_payhub'), ['rows' => 3, 'cols' => 30]);
        $mform->setType('rates', PARAM_TEXT);
        $mform->addHelpButton('rates', 'rates', 'paygw_payhub');
    }

    /**
     * Rates must parse, and at least one gateway must be offered to enable the hub.
     *
     * @param \core_payment\form\account_gateway $form
     * @param \stdClass $data
     * @param array $files
     * @param array $errors
     */
    public static function validate_gateway_form(
        \core_payment\form\account_gateway $form,
        \stdClass $data,
        array $files,
        array &$errors
    ): void {
        if ($bad = pricing::bad_lines((string) ($data->rates ?? ''))) {
            $errors['rates'] = get_string('error:rates', 'paygw_payhub', s(reset($bad)));
        }
        if (!empty($data->enabled)) {
            $any = false;
            foreach (array_keys(registry::installed()) as $name) {
                $any = $any || !empty($data->{'use_' . $name});
            }
            if (!$any) {
                $errors['enabled'] = get_string('error:noneoffered', 'paygw_payhub');
            }
        }
    }
}
