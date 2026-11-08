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
 * The providers the hub can offer: installed gateway plugins that ship a payhub\provider adapter.
 *
 * @package    paygw_payhub
 * @copyright  2026 DevLMS <contact@devlms.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class registry {
    /** @var string[]|null For tests: gateway name => adapter class, instead of the installed plugins. */
    public static ?array $override = null;

    /**
     * Every installed provider, keyed by gateway name.
     *
     * @return string[] gateway name => adapter class name
     */
    public static function installed(): array {
        if (self::$override !== null) {
            return self::$override;
        }
        $out = [];
        foreach (\core_component::get_plugin_list_with_class('paygw', 'payhub\provider') as $component => $class) {
            if ($component !== 'paygw_payhub' && is_subclass_of($class, provider::class)) {
                $out[$class::name()] = $class;
            }
        }
        ksort($out);
        return $out;
    }

    /**
     * Providers a payment account can use now: installed, the gateway enabled for the site and set up in the account,
     * and switched on in the hub's settings for that account.
     *
     * @param \core_payment\account $account
     * @param array $hubconfig The hub's configuration in that account.
     * @return array gateway name => [class, config, methods]
     */
    public static function usable(\core_payment\account $account, array $hubconfig): array {
        $gateways = $account->get_gateways(false);
        $enabled = \core\plugininfo\paygw::get_enabled_plugins() ?? [];
        $out = [];
        foreach (self::installed() as $name => $class) {
            $gateway = $gateways[$name] ?? null;
            if (!$gateway || !isset($enabled[$name]) || empty($hubconfig['use_' . $name])) {
                continue;
            }
            $config = $gateway->get_configuration();
            $methods = $class::methods($config);
            if ($methods) {
                $out[$name] = ['class' => $class, 'config' => $config, 'methods' => $methods];
            }
        }
        return $out;
    }
}
