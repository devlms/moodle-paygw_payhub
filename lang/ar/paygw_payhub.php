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

/**
 * Strings for paygw_payhub (Arabic).
 *
 * @package    paygw_payhub
 * @copyright  2026 DevLMS <contact@devlms.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['activate'] = 'تفعيل (يثبّت الإصدار {$a})';
$string['checkout'] = 'اختر طريقة الدفع';
$string['code'] = 'الشيفرة';
$string['enable'] = 'تفعيل';
$string['enabled'] = 'تمّ تفعيل {$a}. اضبطه في حساب الدفع ثم اعرضه في المركز.';
$string['error:checksum'] = 'الملف المنزَّل لـ {$a} لا يطابق البصمة التي ينشرها GitHub لذلك الإصدار، لذلك لم يُثبَّت شيء.';
$string['error:download'] = 'تعذّر تنزيل {$a}.';
$string['error:noneoffered'] = 'اعرض بوابة واحدة على الأقل لتفعيل المركز.';
$string['error:nooption'] = 'طريقة الدفع هذه غير متاحة لهذا العنصر.';
$string['error:norelease'] = 'لم يُعثر على GitHub على إصدار يحتوي ملف تثبيت لـ {$a}.';
$string['error:notwritable'] = 'مجلد بوابات الدفع على الخادم غير قابل للكتابة، لذلك لا يمكن تثبيت البوابات من هنا. ثبّتها يدويًا.';
$string['error:rates'] = 'سعر غير صالح: «{$a}». اكتب سعرًا في كل سطر، مثل USD DZD 135.5.';
$string['error:unknowngateway'] = 'لا يعرف المركز بوابة باسم {$a}.';
$string['error:wrongplugin'] = 'الملف المنزَّل لا يحتوي بوابة {$a}، لذلك لم يُثبَّت شيء.';
$string['event:paymentcompleted'] = 'اكتمل الدفع عبر المركز';
$string['feecurrencies'] = 'عملات الأسعار القابلة للتحويل';
$string['feecurrencies_desc'] = 'عملات يمكن تحديد السعر بها حتى إن لم تكن هناك بوابة تقبض بها، لأن أسعار الصرف في الحساب تحوّلها. مثال: USD, EUR. يمكن عندئذ دفع ثمن دورة مسعّرة بالدولار بالدينار ببطاقة محلية.';
$string['gatewaydescription'] = 'الدفع المحلي والدولي في صفحة واحدة، ولكلٍّ سعره.';
$string['gatewayname'] = 'الدفع المحلي أو الدولي';
$string['guide'] = 'دليل الإعداد';
$string['manualinstall'] = 'لا يمكن لهذا الخادم تثبيتها من هنا. <a href="{$a}">نزّل الإصدار</a> وفُكّ ضغطه في payment/gateway، ثم افتح إدارة الموقع > الإشعارات.';
$string['nooptions'] = 'لم تُضبط أي طريقة دفع لهذا العنصر بعد. اتصل بمدير الموقع.';
$string['noproviders'] = 'لم تُثبَّت أي بوابة بعد. <a href="{$a}">اختر البوابات المراد تثبيتها</a>.';
$string['offer'] = 'اعرض عند الدفع';
$string['offer_help'] = 'البوابات المثبتة على هذا الموقع التي يعرضها حساب الدفع هذا عبر المركز. تُضبط مفاتيح كل بوابة في قسمها الخاص من حساب الدفع هذا.';
$string['pluginname'] = 'مركز الدفع';
$string['pluginname_desc'] = 'بوابات دفع محلية ودولية في صفحة دفع واحدة.';
$string['privacy:metadata:paygw_payhub'] = 'المدفوعات التي بدأها المركز عبر بوابة.';
$string['privacy:metadata:paygw_payhub:amount'] = 'المبلغ المطلوب بعملة البوابة.';
$string['privacy:metadata:paygw_payhub:currency'] = 'العملة المقتطعة.';
$string['privacy:metadata:paygw_payhub:provider'] = 'البوابة المستخدمة.';
$string['privacy:metadata:paygw_payhub:status'] = 'حالة الدفع: قيد الانتظار أو مدفوعة.';
$string['privacy:metadata:paygw_payhub:timecreated'] = 'تاريخ بدء عملية الدفع.';
$string['privacy:metadata:paygw_payhub:userid'] = 'الشخص الذي يدفع.';
$string['providers'] = 'البوابات';
$string['providersintro'] = 'البوابات التي يمكن للمركز عرضها. زر التفعيل يثبّت البوابة؛ اضبط بعدها مفاتيحها في <a href="{$a}">حساب دفع</a> واعرضها في قسم المركز من ذلك الحساب.';
$string['rates'] = 'أسعار الصرف';
$string['rates_help'] = 'تُستخدم عندما يكون السعر بعملة وتقبض البوابة بعملة أخرى. سعر في كل سطر: USD DZD 135.5 يعني أن 1 دولار يساوي 135.5 دينار. تُقرَّب الأسعار المحوَّلة إلى الأعلى.';
$string['region:international'] = 'الدفع الدولي';
$string['region:local'] = 'الدفع المحلي';
$string['state:enabled'] = 'مفعّلة';
