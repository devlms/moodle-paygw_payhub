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
 * The newest signed release of a gateway, read from GitHub: the release zip, the SHA-256 GitHub publishes for
 * it, and its signature file.
 *
 * Prereleases count while a gateway has no stable release yet. Answers are kept for an hour, so the Gateways
 * page does not use up GitHub's limit for anonymous calls.
 *
 * @package    paygw_payhub
 * @copyright  2026 DevLMS <contact@devlms.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class releases {
    /** @var int Seconds an answer from GitHub is kept. */
    private const TTL = HOURSECS;

    /** @var callable|null For tests: url => JSON text. */
    public static $http = null;

    /**
     * The newest release of a gateway that has an install zip, or null when GitHub has none or cannot be reached.
     *
     * @param string $name Gateway name, such as chargily.
     * @param string $repo owner/name on GitHub.
     * @param bool $fresh Ask GitHub even if a recent answer is kept.
     * @return array|null tag, release (version text), url, sha256, sigurl
     */
    public static function latest(string $name, string $repo, bool $fresh = false): ?array {
        $key = 'release_' . $name;
        $kept = json_decode((string) get_config('paygw_payhub', $key), true);
        if (!$fresh && is_array($kept) && ($kept['time'] ?? 0) > time() - self::TTL) {
            return $kept['release'];
        }
        $release = self::fetch($name, $repo);
        set_config($key, json_encode(['time' => time(), 'release' => $release]), 'paygw_payhub');
        return $release;
    }

    /**
     * Ask GitHub.
     *
     * @param string $name
     * @param string $repo
     * @return array|null
     */
    private static function fetch(string $name, string $repo): ?array {
        global $CFG;
        if (!preg_match('~^[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+$~', $repo)) {
            return null;
        }
        $url = "https://api.github.com/repos/$repo/releases?per_page=10";
        if (self::$http) {
            $body = (self::$http)($url);
        } else {
            require_once($CFG->libdir . '/filelib.php');
            $curl = new \curl();
            $curl->setHeader(['Accept: application/vnd.github+json', 'User-Agent: Moodle-paygw_payhub']);
            $body = $curl->get($url, [], ['CURLOPT_TIMEOUT' => 15, 'CURLOPT_CONNECTTIMEOUT' => 6]);
            if ($curl->get_errno() || (int) ($curl->get_info()['http_code'] ?? 0) !== 200) {
                return null;
            }
        }
        $list = json_decode((string) $body, true);
        if (!is_array($list)) {
            return null;
        }
        foreach ($list as $release) {
            if (!empty($release['draft'])) {
                continue;
            }
            $assets = $release['assets'] ?? [];
            $urls = array_column($assets, 'browser_download_url', 'name');
            foreach ($assets as $asset) {
                $digest = (string) ($asset['digest'] ?? '');
                $sigurl = (string) ($urls[($asset['name'] ?? '') . '.sig'] ?? '');
                if (
                    preg_match('/^paygw_' . preg_quote($name, '/') . '-[0-9A-Za-z.+-]+\.zip$/', (string) ($asset['name'] ?? ''))
                    && preg_match('/^sha256:([0-9a-f]{64})$/', $digest, $m)
                    && str_starts_with((string) ($asset['browser_download_url'] ?? ''), 'https://github.com/' . $repo . '/')
                    && str_starts_with($sigurl, 'https://github.com/' . $repo . '/')
                ) {
                    return [
                        'tag' => (string) $release['tag_name'],
                        'release' => ltrim((string) $release['tag_name'], 'v'),
                        'url' => (string) $asset['browser_download_url'],
                        'sha256' => $m[1],
                        'sigurl' => $sigurl,
                    ];
                }
            }
        }
        return null;
    }
}
