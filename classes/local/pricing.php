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
 * Turns the item's price into a provider's currency.
 *
 * The fee keeps its own currency in Moodle. For a provider in another currency the payment account gives a rate,
 * one line per pair: "USD DZD 135.5" means 1 USD costs 135.5 DZD. Amounts are rounded to the target currency's
 * decimals, and up rather than down, so a converted price is never lower than the fee.
 *
 * @package    paygw_payhub
 * @copyright  2026 DevLMS <contact@devlms.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class pricing {
    /** @var int[] Currencies whose minor unit is not 2 decimals. */
    private const DECIMALS = ['BHD' => 3, 'IQD' => 3, 'JOD' => 3, 'KWD' => 3, 'LYD' => 3, 'OMR' => 3, 'TND' => 3,
        'JPY' => 0, 'KRW' => 0, 'XOF' => 0, 'XAF' => 0, 'CLP' => 0, 'VND' => 0, 'UGX' => 0, 'ISK' => 0];

    /**
     * Rates from the hub's account setting.
     *
     * @param string $text One "FROM TO RATE" per line.
     * @return array "FROM TO" => rate
     */
    public static function parse_rates(string $text): array {
        $rates = [];
        foreach (preg_split('/\R/', $text) as $line) {
            $ok = preg_match('/^\s*([A-Z]{3})\s+([A-Z]{3})\s+([0-9]+(?:\.[0-9]+)?)\s*$/', strtoupper($line), $m);
            if ($ok && (float) $m[3] > 0) {
                $rates[$m[1] . ' ' . $m[2]] = (float) $m[3];
            }
        }
        return $rates;
    }

    /**
     * Lines of the setting that are not a valid rate.
     *
     * @param string $text
     * @return string[]
     */
    public static function bad_lines(string $text): array {
        return array_values(array_filter(preg_split('/\R/', $text), fn($l) => trim($l) !== '' && !self::parse_rates($l)));
    }

    /**
     * The price in another currency, or null when there is no rate.
     *
     * @param float $amount
     * @param string $from
     * @param string $to
     * @param array $rates From parse_rates().
     * @return float|null
     */
    public static function convert(float $amount, string $from, string $to, array $rates): ?float {
        if ($from === $to) {
            return $amount;
        }
        if (isset($rates["$from $to"])) {
            $value = $amount * $rates["$from $to"];
        } else if (isset($rates["$to $from"])) {
            $value = $amount / $rates["$to $from"];
        } else {
            return null;
        }
        $factor = 10 ** self::decimals($to);
        // Round up, after trimming float noise such as 2500.0000000001.
        return ceil(round($value * $factor, 6)) / $factor;
    }

    /**
     * Decimals of a currency's minor unit.
     *
     * @param string $currency
     * @return int
     */
    public static function decimals(string $currency): int {
        return self::DECIMALS[$currency] ?? 2;
    }
}
