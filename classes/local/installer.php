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

namespace paygw_payhub\local;

/**
 * Installs a gateway from the catalogue: download the pinned release, check its SHA-256, unpack it into
 * payment/gateway with Moodle's own code manager. Moodle's upgrade screen then finishes the install.
 *
 * @package    paygw_payhub
 * @copyright  2026 DevLMS <contact@devlms.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class installer {
    /**
     * Whether this site can install gateways from the web: the gateway folder must be writable.
     *
     * @return bool
     */
    public static function can_install(): bool {
        return \core_plugin_manager::instance()->is_plugintype_writable('paygw');
    }

    /**
     * Download, check and unpack one gateway.
     *
     * @param string $name Catalogue name.
     * @param callable|null $fetch For tests: url => zip content.
     */
    public static function install(string $name, ?callable $fetch = null): void {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');
        require_capability('moodle/site:config', \context_system::instance());
        $entry = catalogue::get($name);
        if (!$entry) {
            throw new \moodle_exception('error:unknowngateway', 'paygw_payhub', '', $name);
        }
        if (array_key_exists($name, \core_component::get_plugin_list('paygw'))) {
            return;
        }
        if (!self::can_install()) {
            throw new \moodle_exception('error:notwritable', 'paygw_payhub');
        }
        if (empty($entry['sha256'])) {
            throw new \moodle_exception('error:checksum', 'paygw_payhub', '', $name);
        }
        $zip = $fetch ? $fetch($entry['url']) : download_file_content($entry['url'], null, null, false, 120, 20);
        if (!is_string($zip) || $zip === '') {
            throw new \moodle_exception('error:download', 'paygw_payhub', '', $entry['url']);
        }
        // Nothing is unpacked unless the file is exactly the release the hub was built with.
        if (!hash_equals($entry['sha256'], hash('sha256', $zip))) {
            throw new \moodle_exception('error:checksum', 'paygw_payhub', '', $name);
        }
        $path = make_request_directory() . '/' . $name . '.zip';
        file_put_contents($path, $zip);
        $manager = new \core\update\code_manager();
        $root = $manager->get_plugin_zip_root_dir($path);
        if ($root === false) {
            throw new \moodle_exception('error:download', 'paygw_payhub', '', $entry['url']);
        }
        $target = \core_plugin_manager::instance()->get_plugintype_root('paygw');
        $files = $manager->unzip_plugin_file($path, $target, $name);
        if (!$files) {
            throw new \moodle_exception('error:download', 'paygw_payhub', '', $entry['url']);
        }
        \core_component::reset();
        \core_plugin_manager::reset_caches();
    }
}
