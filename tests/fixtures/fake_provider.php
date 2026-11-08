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

namespace paygw_payhub\tests;

use paygw_payhub\request;

/**
 * A provider for tests: takes "card" payments in USD and records what it was asked.
 *
 * It borrows the name of core's PayPal gateway so it has a payment account section on every site.
 *
 * @package    paygw_payhub
 * @copyright  2026 DevLMS <contact@devlms.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class fake_provider implements \paygw_payhub\provider {
    /** @var request|null The last request. */
    public static ?request $last = null;

    /**
     * Name.
     *
     * @return string
     */
    public static function name(): string {
        return 'paypal';
    }

    /**
     * Region.
     *
     * @return string
     */
    public static function region(): string {
        return self::INTERNATIONAL;
    }

    /**
     * Currencies.
     *
     * @return string[]
     */
    public static function currencies(): array {
        return ['USD'];
    }

    /**
     * Methods.
     *
     * @param array $config
     * @return string[]
     */
    public static function methods(array $config): array {
        return ['card' => 'Visa or Mastercard'];
    }

    /**
     * Start.
     *
     * @param request $request
     * @return string
     */
    public static function start(request $request): string {
        self::$last = $request;
        return 'https://example.com/checkout/' . $request->reference;
    }
}
