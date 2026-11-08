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

use paygw_payhub\local\catalogue;
use paygw_payhub\local\installer;
use paygw_payhub\local\pricing;
use paygw_payhub\tests\fake_provider;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/fixtures/fake_provider.php');

/**
 * The hub: options and prices, starting a payment, completing it, installing gateways.
 *
 * @package    paygw_payhub
 * @category   test
 * @copyright  2026 DevLMS <contact@devlms.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \paygw_payhub\hub
 * @covers     \paygw_payhub\registry
 * @covers     \paygw_payhub\gateway
 * @covers     \paygw_payhub\local\pricing
 * @covers     \paygw_payhub\local\installer
 * @covers     \paygw_payhub\local\catalogue
 * @covers     \paygw_payhub\event\payment_completed
 */
final class hub_test extends \advanced_testcase {
    /** @var int enrol_fee instance */
    private int $instanceid;
    /** @var \stdClass */
    private \stdClass $course;
    /** @var \stdClass */
    private \stdClass $student;

    protected function setUp(): void {
        global $DB;
        parent::setUp();
        $this->resetAfterTest();
        registry::$override = ['paypal' => fake_provider::class];
        catalogue::$override = null;
        set_config('enrol_plugins_enabled', 'manual,fee');
        \core\plugininfo\paygw::enable_plugin('payhub', 1);
        \core\plugininfo\paygw::enable_plugin('paypal', 1);
        $gen = $this->getDataGenerator();
        $account = $gen->get_plugin_generator('core_payment')->create_payment_account();
        \core_payment\helper::save_payment_gateway((object) ['accountid' => $account->get('id'), 'gateway' => 'paypal',
            'enabled' => 0, 'config' => json_encode(['clientid' => 'x', 'secret' => 'y'])]);
        \core_payment\helper::save_payment_gateway((object) ['accountid' => $account->get('id'), 'gateway' => 'payhub',
            'enabled' => 1, 'config' => json_encode(['use_paypal' => 1, 'rates' => "USD DZD 135"])]);
        $this->course = $gen->create_course();
        $this->instanceid = (int) enrol_get_plugin('fee')->add_instance($this->course, [
            'cost' => 2700, 'currency' => 'DZD', 'customint1' => $account->get('id'),
            'roleid' => $DB->get_field('role', 'id', ['shortname' => 'student']),
        ]);
        $this->student = $gen->create_user();
        $this->setUser($this->student);
    }

    protected function tearDown(): void {
        registry::$override = null;
        catalogue::$override = null;
        parent::tearDown();
    }

    public function test_options_convert_the_price_into_each_providers_currency(): void {
        $options = hub::options('enrol_fee', 'fee', $this->instanceid);
        $this->assertCount(1, $options);
        $this->assertSame('USD', $options[0]['currency']);
        // 2700 DZD at 135 DZD per USD.
        $this->assertEquals(20.0, $options[0]['amount']);
        $this->assertSame(['card' => 'Visa or Mastercard'], $options[0]['methods']);
    }

    public function test_a_hub_payment_is_saved_in_the_currency_charged_and_delivered_once(): void {
        global $DB;
        $url = hub::start('enrol_fee', 'fee', $this->instanceid, (int) $this->student->id, 'paypal', 'card', 'Physics');
        $reference = fake_provider::$last->reference;
        $this->assertSame('https://example.com/checkout/' . $reference, $url);
        $this->assertEquals(20.0, fake_provider::$last->amount);

        $this->assertNull(hub::complete($reference, 'paypal', 5.0, 'USD'), 'Less than asked is refused.');
        $this->assertNull(hub::complete($reference, 'paypal', 20.0, 'DZD'), 'Another currency is refused.');
        $this->assertNull(hub::complete('nope', 'paypal', 20.0, 'USD'));
        $sink = $this->redirectEvents();
        $paymentid = hub::complete($reference, 'paypal', 20.0, 'USD');
        $this->assertNotNull($paymentid);
        $done = array_values(array_filter($sink->get_events(), fn($e) => $e instanceof \paygw_payhub\event\payment_completed));
        $this->assertCount(1, $done);
        $this->assertSame('paypal', $done[0]->other['gateway']);
        $sink->close();
        $this->assertTrue(is_enrolled(\context_course::instance($this->course->id), $this->student));
        $payment = $DB->get_record('payments', ['id' => $paymentid]);
        $this->assertSame('USD', $payment->currency);
        $this->assertSame($paymentid, hub::complete($reference, 'paypal', 20.0, 'USD'), 'A second call returns the same payment.');
        $this->assertSame(1, $DB->count_records('payments'));
        $this->assertStringContainsString('/course/view.php', hub::success_url($reference)->out(false));
    }

    public function test_a_provider_the_account_does_not_offer_is_refused(): void {
        global $DB;
        $gw = $DB->get_record('payment_gateways', ['gateway' => 'payhub']);
        $DB->set_field('payment_gateways', 'config', json_encode(['use_paypal' => 0]), ['id' => $gw->id]);
        $this->assertSame([], hub::options('enrol_fee', 'fee', $this->instanceid));
        $this->expectException(\moodle_exception::class);
        hub::start('enrol_fee', 'fee', $this->instanceid, (int) $this->student->id, 'paypal', 'card', 'Physics');
    }

    public function test_pricing_rounds_up_to_the_currency_decimals(): void {
        $rates = pricing::parse_rates("usd dzd 135.5\nKWD USD 3.25\nnonsense");
        $this->assertSame(['USD DZD' => 135.5, 'KWD USD' => 3.25], $rates);
        $this->assertEquals(14.77, pricing::convert(2000, 'DZD', 'USD', $rates), 'Inverse rate, rounded up to cents.');
        $this->assertEquals(6.154, pricing::convert(20, 'USD', 'KWD', $rates), 'Three decimals for KWD.');
        $this->assertNull(pricing::convert(10, 'EUR', 'DZD', $rates));
        $this->assertSame(['nonsense'], pricing::bad_lines("USD DZD 1\nnonsense"));
        $this->assertSame(3, pricing::decimals('KWD'));
    }

    public function test_installer_refuses_a_download_that_does_not_match_its_checksum(): void {
        $this->setAdminUser();
        catalogue::$override = ['nosuchgw' => ['region' => 'local', 'countries' => ['DZ'], 'currencies' => ['DZD'],
            'methods' => [], 'release' => '1.0', 'version' => 1, 'url' => 'https://example.com/x.zip',
            'sha256' => hash('sha256', 'the real zip'), 'repo' => '', 'guide' => '']];
        if (!installer::can_install()) {
            $this->markTestSkipped('The gateway folder is not writable here.');
        }
        try {
            installer::install('nosuchgw', fn($url) => 'a tampered zip');
            $this->fail('Installed a file with the wrong checksum');
        } catch (\moodle_exception $e) {
            $this->assertSame('error:checksum', $e->errorcode);
        }
        $this->assertArrayNotHasKey('nosuchgw', \core_component::get_plugin_list('paygw'));
    }

    public function test_supported_currencies_include_the_fee_currencies_for_conversion(): void {
        set_config('feecurrencies', 'USD, eur', 'paygw_payhub');
        $currencies = gateway::get_supported_currencies();
        $this->assertContains('USD', $currencies);
        $this->assertContains('EUR', $currencies);
    }
}
