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
 * What a payment gateway implements to be offered by the hub.
 *
 * A gateway plugin ships its adapter as \paygw_<name>\payhub\provider. Only the hub loads that class, so the
 * gateway works without the hub, and the hub works with whichever gateways are installed.
 *
 * @package    paygw_payhub
 * @copyright  2026 DevLMS <contact@devlms.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface provider {
    /** @var string Local payment systems: local cards and wallets, in the local currency. */
    public const LOCAL = 'local';
    /** @var string International cards and wallets. */
    public const INTERNATIONAL = 'international';

    /**
     * The gateway's plugin name, such as chargily.
     *
     * @return string
     */
    public static function name(): string;

    /**
     * Local or international.
     *
     * @return string self::LOCAL or self::INTERNATIONAL
     */
    public static function region(): string;

    /**
     * Currencies this provider charges in (ISO 4217).
     *
     * @return string[]
     */
    public static function currencies(): array;

    /**
     * Payment methods a payment account has turned on, as key => label (EDAHABIA, CIB...).
     *
     * @param array $config The gateway's own configuration in the payment account.
     * @return string[]
     */
    public static function methods(array $config): array;

    /**
     * Start a payment and return the URL to send the payer to.
     *
     * When the provider confirms the payment, the gateway calls \paygw_payhub\hub::complete() with the request's
     * reference and the amount and currency actually charged.
     *
     * @param request $request
     * @return string
     */
    public static function start(request $request): string;
}
