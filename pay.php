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
 * The hub's checkout: pay with a local card in the local currency, or internationally, each at its own price.
 *
 * @package    paygw_payhub
 * @copyright  2026 DevLMS <contact@devlms.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_payment\helper;
use paygw_payhub\hub;

require_once(__DIR__ . '/../../../config.php');

require_login();
$component = required_param('component', PARAM_COMPONENT);
$paymentarea = required_param('paymentarea', PARAM_AREA);
$itemid = required_param('itemid', PARAM_INT);
$description = optional_param('description', '', PARAM_TEXT);
$choice = optional_param('choice', '', PARAM_TEXT);

$params = ['component' => $component, 'paymentarea' => $paymentarea, 'itemid' => $itemid, 'description' => $description];
$PAGE->set_url(new moodle_url('/payment/gateway/payhub/pay.php', $params));
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('checkout', 'paygw_payhub'));
$PAGE->set_heading(get_string('checkout', 'paygw_payhub'));

if ($choice !== '' && confirm_sesskey()) {
    [$provider, $method] = array_pad(explode(':', $choice, 2), 2, '');
    try {
        $url = hub::start(
            $component,
            $paymentarea,
            $itemid,
            (int) $USER->id,
            clean_param($provider, PARAM_PLUGIN),
            clean_param($method, PARAM_ALPHANUMEXT),
            $description
        );
    } catch (moodle_exception $e) {
        redirect($PAGE->url, $e->getMessage(), null, \core\output\notification::NOTIFY_ERROR);
    }
    redirect($url);
}

$groups = [];
foreach (hub::options($component, $paymentarea, $itemid) as $o) {
    $groups[$o['region']]['label'] = get_string('region:' . $o['region'], 'paygw_payhub');
    $groups[$o['region']]['options'][] = [
        'price' => helper::get_cost_as_string($o['amount'], $o['currency']),
        'provider' => get_string('pluginname', 'paygw_' . $o['provider']),
        'methods' => array_map(
            fn($k, $label) => ['value' => $o['provider'] . ':' . $k, 'label' => $label],
            array_keys($o['methods']),
            $o['methods']
        ),
    ];
}
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('paygw_payhub/checkout', [
    'description' => $description,
    'groups' => array_values($groups),
    'hasoptions' => (bool) $groups,
    'action' => $PAGE->url->out(false),
    'sesskey' => sesskey(),
    'hidden' => array_map(fn($k, $v) => ['name' => $k, 'value' => $v], array_keys($params), $params),
]);
echo $OUTPUT->footer();
