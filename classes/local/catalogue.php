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
 * The gateways the hub knows how to install, each pinned to one release and its SHA-256 checksum.
 *
 * A new gateway, or a new release of one, comes with a new release of the hub: nothing is fetched from a list
 * that could change under the site.
 *
 * @package    paygw_payhub
 * @copyright  2026 DevLMS <contact@devlms.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class catalogue {
    /** @var array[]|null For tests: entries instead of the built-in ones. */
    public static ?array $override = null;

    /** @var array[] gateway name => entry */
    private const ENTRIES = [
        'chargily' => [
            'region' => 'local',
            'countries' => ['DZ'],
            'currencies' => ['DZD'],
            'methods' => ['EDAHABIA', 'CIB'],
            'release' => '0.1.0',
            'version' => 2026110800,
            'url' => 'https://github.com/devlms/moodle-paygw_chargily/releases/download/v0.1.0/paygw_chargily-0.1.0.zip',
            'sha256' => '',
            'repo' => 'https://github.com/devlms/moodle-paygw_chargily',
            'guide' => 'https://devlms.com/guides/chargily-moodle/',
        ],
    ];

    /**
     * Every entry.
     *
     * @return array[]
     */
    public static function all(): array {
        return self::$override ?? self::ENTRIES;
    }

    /**
     * One entry.
     *
     * @param string $name
     * @return array|null
     */
    public static function get(string $name): ?array {
        return self::all()[$name] ?? null;
    }
}
