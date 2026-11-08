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
 * Strings for paygw_payhub (French).
 *
 * @package    paygw_payhub
 * @copyright  2026 DevLMS <contact@devlms.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['activate'] = 'Activer (installe la version {$a})';
$string['checkout'] = 'Choisissez votre moyen de paiement';
$string['code'] = 'Code';
$string['enable'] = 'Activer';
$string['enabled'] = '{$a} est activé. Configurez-le dans le compte de paiement, puis proposez-le dans le hub.';
$string['error:checksum'] = 'Le téléchargement de {$a} ne correspond pas à la version prévue par ce hub : rien n\'a été installé.';
$string['error:download'] = 'Impossible de télécharger {$a}.';
$string['error:noneoffered'] = 'Proposez au moins une passerelle pour activer le hub.';
$string['error:nooption'] = 'Ce moyen de paiement n\'est pas disponible pour cet article.';
$string['error:notwritable'] = 'Le dossier des passerelles de paiement n\'est pas accessible en écriture sur le serveur : installez les passerelles manuellement.';
$string['error:rates'] = 'Taux invalide : « {$a} ». Un taux par ligne, par exemple USD DZD 135.5.';
$string['error:unknowngateway'] = 'Le hub ne connaît pas de passerelle nommée {$a}.';
$string['feecurrencies'] = 'Devises de prix pour la conversion';
$string['feecurrencies_desc'] = 'Devises dans lesquelles un prix peut être fixé même si aucune passerelle n\'encaisse dans cette devise, grâce aux taux du compte. Exemple : USD, EUR. Un cours au prix en USD peut alors être payé en DZD avec une carte locale.';
$string['gatewaydescription'] = 'Paiement local et international sur une seule page, chacun avec son propre prix.';
$string['gatewayname'] = 'Paiement local ou international';
$string['guide'] = 'Guide d\'installation';
$string['manualinstall'] = 'Ce serveur ne peut pas l\'installer d\'ici. <a href="{$a}">Téléchargez la version</a>, décompressez-la dans payment/gateway, puis ouvrez Administration du site > Notifications.';
$string['nooptions'] = 'Aucun moyen de paiement n\'est encore configuré pour cet article. Contactez l\'administrateur du site.';
$string['noproviders'] = 'Aucune passerelle n\'est encore installée. <a href="{$a}">Choisir les passerelles à installer</a>.';
$string['offer'] = 'Proposer au paiement';
$string['offer_help'] = 'Passerelles installées sur ce site que ce compte de paiement propose via le hub. Les clés de chaque passerelle se règlent dans sa propre section de ce compte.';
$string['pluginname'] = 'Hub de paiement';
$string['pluginname_desc'] = 'Passerelles de paiement locales et internationales sur une seule page.';
$string['privacy:metadata:paygw_payhub'] = 'Les paiements lancés par le hub via une passerelle.';
$string['privacy:metadata:paygw_payhub:amount'] = 'Le montant demandé, dans la devise de la passerelle.';
$string['privacy:metadata:paygw_payhub:currency'] = 'La devise débitée.';
$string['privacy:metadata:paygw_payhub:provider'] = 'La passerelle utilisée.';
$string['privacy:metadata:paygw_payhub:status'] = 'Si le paiement est en attente ou payé.';
$string['privacy:metadata:paygw_payhub:timecreated'] = 'La date de début du paiement.';
$string['privacy:metadata:paygw_payhub:userid'] = 'La personne qui paie.';
$string['providers'] = 'Passerelles';
$string['providersintro'] = 'Les passerelles que le hub peut proposer. Activer installe une passerelle ; réglez ensuite ses clés dans un <a href="{$a}">compte de paiement</a> et proposez-la dans la section du hub de ce compte.';
$string['rates'] = 'Taux de change';
$string['rates_help'] = 'Utilisés quand un prix est dans une devise et la passerelle encaisse dans une autre. Un taux par ligne : USD DZD 135.5 signifie que 1 USD vaut 135,5 DZD. Les prix convertis sont arrondis au supérieur.';
$string['region:international'] = 'Paiement international';
$string['region:local'] = 'Paiement local';
$string['state:enabled'] = 'Activée';
$string['event:paymentcompleted'] = 'Paiement effectué via le hub';
