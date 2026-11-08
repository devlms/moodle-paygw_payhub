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
 * The second check on a gateway release: an Ed25519 signature made with the DevLMS release key.
 *
 * GitHub's SHA-256 proves the file is the one attached to the release; the signature proves the release was made
 * with our key, so someone who got into the GitHub repository still cannot ship a gateway the hub will install.
 *
 * @package    paygw_payhub
 * @copyright  2026 DevLMS <contact@devlms.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class signature {
    /** @var string The DevLMS release public key (Ed25519, base64). */
    public const PUBLIC_KEY = '5dzReZgXfSHqHK0D0eDTqntq2t2WR17FU1ayM/G90dg=';

    /** @var string|null For tests: another public key. */
    public static ?string $publickey = null;

    /**
     * Whether a file carries a valid signature.
     *
     * @param string $data The file.
     * @param string $signature Base64 signature, as in the release's .sig file.
     * @return bool
     */
    public static function valid(string $data, string $signature): bool {
        $sig = base64_decode(trim($signature), true);
        $key = base64_decode(self::$publickey ?? self::PUBLIC_KEY, true);
        if (
            $sig === false || strlen($sig) !== SODIUM_CRYPTO_SIGN_BYTES || $key === false
                || strlen($key) !== SODIUM_CRYPTO_SIGN_PUBLICKEYBYTES
        ) {
            return false;
        }
        return sodium_crypto_sign_verify_detached($sig, $data, $key);
    }
}
