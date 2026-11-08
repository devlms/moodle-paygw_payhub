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

/**
 * One payment the hub asks a provider to take.
 *
 * @package    paygw_payhub
 * @copyright  2026 DevLMS <contact@devlms.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class request {
    /**
     * Constructor.
     *
     * @param string $component Component of the item paid for.
     * @param string $paymentarea
     * @param int $itemid
     * @param int $userid Who pays.
     * @param string $method One of the provider's method keys.
     * @param float $amount In major units of $currency (dinars, dollars), already converted by the hub.
     * @param string $currency ISO 4217.
     * @param string $description What is paid for.
     * @param string $reference The hub's reference; the provider hands it back to hub::complete().
     */
    public function __construct(
        /** @var string */
        public readonly string $component,
        /** @var string */
        public readonly string $paymentarea,
        /** @var int */
        public readonly int $itemid,
        /** @var int */
        public readonly int $userid,
        /** @var string */
        public readonly string $method,
        /** @var float */
        public readonly float $amount,
        /** @var string */
        public readonly string $currency,
        /** @var string */
        public readonly string $description,
        /** @var string */
        public readonly string $reference
    ) {
    }
}
