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
 * The gateways the hub can offer: install, enable, and set up.
 *
 * @package    paygw_payhub
 * @copyright  2026 DevLMS <contact@devlms.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use paygw_payhub\local\catalogue;
use paygw_payhub\local\installer;
use paygw_payhub\local\releases;
use paygw_payhub\registry;

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

require_admin();
$context = context_system::instance();
$url = new moodle_url('/payment/gateway/payhub/providers.php');
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('providers', 'paygw_payhub'));
$PAGE->set_heading(get_string('pluginname', 'paygw_payhub'));

$action = optional_param('action', '', PARAM_ALPHA);
$name = optional_param('name', '', PARAM_PLUGIN);
if ($action !== '' && $name !== '') {
    require_sesskey();
    if ($action === 'install') {
        try {
            installer::install($name);
        } catch (moodle_exception $e) {
            redirect($url, $e->getMessage(), null, \core\output\notification::NOTIFY_ERROR);
        }
        // Moodle's upgrade screen finishes the install, then the admin comes back here.
        redirect(new moodle_url('/admin/index.php'));
    }
    if ($action === 'enable' && array_key_exists($name, core_component::get_plugin_list('paygw'))) {
        \core\plugininfo\paygw::enable_plugin($name, 1);
        redirect(
            $url,
            get_string('enabled', 'paygw_payhub', get_string('pluginname', 'paygw_' . $name)),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    }
}

$installed = registry::installed();
$enabled = \core\plugininfo\paygw::get_enabled_plugins() ?? [];
$canwrite = installer::can_install();
$groups = ['local' => [], 'international' => []];
foreach (catalogue::all() as $key => $entry) {
    $isinstalled = isset($installed[$key]);
    // Only gateways that are not installed need the newest release; GitHub answers are kept for an hour.
    $release = $isinstalled ? null : releases::latest($key, $entry['repo']);
    $groups[$entry['region']][] = [
        'name' => $key,
        'title' => $isinstalled ? get_string('pluginname', 'paygw_' . $key) : ucfirst($key),
        'countries' => implode(', ', $entry['countries']),
        'currencies' => implode(', ', $entry['currencies']),
        'methods' => implode(', ', $entry['methods']),
        'release' => $release['release'] ?? '',
        'guide' => $entry['guide'],
        'repo' => 'https://github.com/' . $entry['repo'],
        'installed' => $isinstalled,
        'enabled' => $isinstalled && isset($enabled[$key]),
        'caninstall' => !$isinstalled && $canwrite && $release,
        'manual' => !$isinstalled && (!$canwrite || !$release),
        'url' => $release['url'] ?? 'https://github.com/' . $entry['repo'] . '/releases',
        'actionurl' => $url->out(false),
        'sesskey' => sesskey(),
    ];
}
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('providers', 'paygw_payhub'));
echo $OUTPUT->render_from_template('paygw_payhub/providers', [
    'groups' => array_values(array_filter(array_map(fn($k) => [
        'label' => get_string('region:' . $k, 'paygw_payhub'),
        'items' => $groups[$k],
    ], array_keys($groups)), fn($g) => $g['items'])),
    'accountsurl' => (new moodle_url('/payment/accounts.php'))->out(false),
]);
echo $OUTPUT->footer();
