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
 * Site settings: currencies a fee may be in for conversion, and the Providers page.
 *
 * @package    paygw_payhub
 * @copyright  2026 DevLMS <contact@devlms.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext(
        'paygw_payhub/feecurrencies',
        get_string('feecurrencies', 'paygw_payhub'),
        get_string('feecurrencies_desc', 'paygw_payhub'),
        'USD, EUR',
        PARAM_TEXT
    ));
    $settings->add(new admin_setting_description(
        'paygw_payhub/providerslink',
        '',
        html_writer::link(
            new moodle_url('/payment/gateway/payhub/providers.php'),
            get_string('providers', 'paygw_payhub'),
            ['class' => 'btn btn-secondary']
        )
    ));
}
