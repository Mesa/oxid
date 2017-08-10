<?php
/**
 * This file is part of OXID eSales PayPal module.
 *
 * OXID eSales PayPal module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales PayPal module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales PayPal module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 */

// -------------------------------
// RESOURCE IDENTIFIER = STRING
// -------------------------------
$aLang = array(
    'charset'                                            => 'ISO-8859-15',
    'SHOP_MODULE_GROUP_oepaypal_display'                 => 'Anzeige auf PayPal-Zahlungsseite',
    'SHOP_MODULE_GROUP_oepaypal_checkout'                => 'Integration von PayPal',
    'SHOP_MODULE_GROUP_oepaypal_payment'                 => 'Warenkorb auf PayPal-Zahlungsseite',
    'SHOP_MODULE_GROUP_oepaypal_transaction'             => 'Geldeinzug',
    'SHOP_MODULE_GROUP_oepaypal_api'                     => 'API-Signatur',
    'SHOP_MODULE_GROUP_oepaypal_development'             => 'Einstellungen f�r Entwicklung',

    'SHOP_MODULE_sOEPayPalBrandName'                     => 'Name des Shops',
    'HELP_SHOP_MODULE_sOEPayPalBrandName'                => 'Tragen Sie hier den Namen Ihres Shops ein, der auf der PayPal-Zahlungsseite angezeigt werden soll.',
    'SHOP_MODULE_sOEPayPalBorderColor'                   => 'Warenkorbumrandungsfarbe f�r die PayPal-Zahlungsseite',
    'HELP_SHOP_MODULE_sOEPayPalBorderColor'              => 'Tragen Sie hier den hexadezimalen Code der Farbe ein, die bei der Anzeige der PayPal-Zahlungsseite verwendet werden soll.',

    'SHOP_MODULE_blOEPayPalStandardCheckout'             => 'PayPal Basis',
    'HELP_SHOP_MODULE_blOEPayPalStandardCheckout'        => 'PayPal wird am Ende des Bestellprozesses als Zahlungsart angeboten. W�hlt der Kunde PayPal, best�tigt er auf der PayPal-Zahlungsseite den Kauf und wird anschlie�end in den Shop zur�ckgeleitet.',
    'SHOP_MODULE_blOEPayPalExpressCheckout'              => 'PayPal Express',
    'HELP_SHOP_MODULE_blOEPayPalExpressCheckout'         => 'Mit dem PayPal-Express-Button gelangt der Kunde direkt zur PayPal-Zahlungsseite, best�tigt den Kauf und wird anschlie�end in den Shop zur�ckgeleitet. Der Shop �bernimmt dabei auch gleich die f�r den Kauf relevanten PayPal-Kundendaten.',
    'SHOP_MODULE_blOEPayPalGuestBuyRole'                 => 'Gastzahlungen erm�glichen',
    'HELP_SHOP_MODULE_blOEPayPalGuestBuyRole'            => 'Der Kunde kann ohne PayPal-Konto bestellen. Er kann erst die Bezahlung abschlie�en und danach entscheiden, ob er die Informationen f�r zuk�nftige Eink�ufe in einem PayPal-Konto speichern will.',

    'SHOP_MODULE_blOEPayPalSendToPayPal'                 => 'Warenkorb bei PayPal anzeigen',
    'HELP_SHOP_MODULE_blOEPayPalSendToPayPal'            => 'Der Warenkorb mit Artikelinformationen, Preisen und Versandkosten wird nach Anmeldung in PayPal angezeigt. Der Kunde kann im Bestellprozess w�hlen, ob diese Daten �bertragen werden sollen. Hinweis: Befinden Sich Artikel in nicht ganzzahliger Menge (z.B. 1,5) im Warenkorb, wird der Warenkorb niemals in PayPal angezeigt, auch wenn diese Option im Bestellprozess aktiviert wurde.',
    'SHOP_MODULE_blOEPayPalDefaultUserChoice'            => 'Voreingestellte Kundenzustimmung',
    'HELP_SHOP_MODULE_blOEPayPalDefaultUserChoice'       => 'Der Kunde muss im Bestellprozess explizit best�tigen, dass der Warenkorb mit Artikelinformationen, Preisen und Versandkosten zu PayPal �bertragen wird. Sie k�nnen hier die Voreinstellung aktivieren, dass der Kunde der �bermittlung der Daten standardm��ig zustimmt.',

    'SHOP_MODULE_sOEPayPalLogoImageOption'               => 'Shop-Logo auf der PayPal-Zahlungsseite',
    'HELP_SHOP_MODULE_sOEPayPalLogoImageOption'          => 'Auf der PayPal-Zahlungsseite kann ein Shop-Logo angezeigt werden. Es ist m�glich, das Standard-Shop-Logo zu verwenden, welches in der Konfigurationsdatei des Shops definiert ist, oder ein spezielles Shop-Logo. Das Logo sollte nicht gr��er als 190px*60px (Breite*H�he) sein. Gr��ere Bilder werden an diese Breite und H�he angepasst und deren Dateinamen mit einem Prefix "resized_" versehen. F�r jedes verwendete Theme muss die Datei mit dem Logo im Verzeichnis /out/{theme}/img vorhanden sein. Wird das vorgesehene Logo nicht angezeigt, �berpr�fen Sie bitte, ob der Dateiname korrekt angegeben wurde und die Datei im Verzeichnis existiert. F�r das Standard-Shop-Logo �berpr�fen Sie den Eintrag "sShopLogo" in der Datei config.inc.php. F�gen Sie diesen Eintrag hinzu, wenn dieser nicht vorhanden ist',

    'SHOP_MODULE_sOEPayPalCustomShopLogoImage'           => 'Spezielles Shop-Logo f�r die PayPal-Zahlungsseite',
    'HELP_SHOP_MODULE_sOEPayPalCustomShopLogoImage'      => 'Auf der PayPal-Zahlungsseite kann ein eigenes Shop-Logo angezeigt werden. Speichern Sie das Logo im Bildverzeichnis des Shops (/out/{theme}/img) und tragen Sie den Dateinamen hier ein. F�r jedes verwendete Theme muss die Datei mit dem Logo im jeweiligen Verzeichnis vorhanden sein.',

    'SHOP_MODULE_sOEPayPalLogoImageOption_noLogo'        => 'Kein Shop-Logo senden',
    'SHOP_MODULE_sOEPayPalLogoImageOption_shopLogo'      => 'Standard-Shop-Logo senden',
    'SHOP_MODULE_sOEPayPalLogoImageOption_customLogo'    => 'Spezielles Shop-Logo senden',

    'SHOP_MODULE_sOEPayPalTransactionMode'               => 'Zeitpunkt des Geldtransfers',
    'HELP_SHOP_MODULE_sOEPayPalTransactionMode'          => 'W�hlen Sie aus, zu welchem Zeitpunkt der Geldtransfer stattfinden soll. Sie haben die M�glichkeit, den Einzug des Geldes auf der PayPal-Seite sofort beim Kauf (SALE), oder erst unmittelbar vor Versand der Ware manuell durchzuf�hren (AUTH). Sie k�nnen auch festlegen, dass der Zeitpunkt des Geldtransfers in Abh�ngigkeit vom Lagerbestand der bestellten Artikel vom Shop automatisch bestimmt wird (AUTOMATIC).',
    'SHOP_MODULE_sOEPayPalTransactionMode_Automatic'     => 'AUTOMATIC - abh�ngig vom Lagerbestand der bestellten Artikel',
    'SHOP_MODULE_sOEPayPalTransactionMode_Sale'          => 'SALE - sofort durchf�hren',
    'SHOP_MODULE_sOEPayPalTransactionMode_Authorization' => 'AUTH - vor Versand manuell durchf�hren',
    'SHOP_MODULE_sOEPayPalEmptyStockLevel'               => 'Restlagerbestand',
    'HELP_SHOP_MODULE_sOEPayPalEmptyStockLevel'          => 'Dieser Wert gilt f�r AUTOMATIC und beeinflusst, ob AUTH oder SALE als Zeitpunkt des Geldtransfers verwendet wird. Es wird gepr�ft, ob nach einer Bestellung der Lagerbestand eines der Produkte kleiner als der definierte Restlagerbestand ist. In diesem Fall wird AUTH als Transfermethode verwendet, ansonsten SALE.',

    'SHOP_MODULE_sOEPayPalUserEmail'                     => 'E-Mail-Adresse des PayPal-Benutzers',
    'SHOP_MODULE_sOEPayPalUsername'                      => 'API-Benutzername',
    'HELP_SHOP_MODULE_sOEPayPalUsername'                 => 'Loggen Sie sich in Ihr <a target="_blank" href="https://www.paypal.com/de/cgi-bin/webscr?cmd=_get-api-signature&generic-flow=true">PayPal-Konto</a> ein, um Ihre API-Signatur zu erhalten.',
    'SHOP_MODULE_sOEPayPalPassword'                      => 'API-Passwort',
    'SHOP_MODULE_sOEPayPalSignature'                     => 'Unterschrift',

    'SHOP_MODULE_blOEPayPalSandboxMode'                  => 'Sandbox aktivieren',
    'SHOP_MODULE_sOEPayPalSandboxUserEmail'              => 'Sandbox: E-Mail-Adresse des PayPal-Benutzers',
    'SHOP_MODULE_sOEPayPalSandboxUsername'               => 'Sandbox: API-Benutzername',
    'HELP_SHOP_MODULE_sOEPayPalSandboxUsername'          => 'Loggen Sie sich in Ihr <a target="_blank" href="https://www.sandbox.paypal.com/de/cgi-bin/webscr?cmd=_get-api-signature&generic-flow=true">PayPal-Konto</a> ein, um Ihre API-Signatur f�r die PayPal-Sandbox zu erhalten.',
    'SHOP_MODULE_sOEPayPalSandboxPassword'               => 'Sandbox: API-Passwort',
    'SHOP_MODULE_sOEPayPalSandboxSignature'              => 'Sandbox: Unterschrift',

    'SHOP_MODULE_blPayPalLoggerEnabled'                  => 'PayPal Logging aktivieren',

    'SHOP_MODULE_blOEPayPalECheckoutInDetails'           => 'Express Checkout auf der Artikel-Detailseite anzeigen',
    'HELP_SHOP_MODULE_blOEPayPalECheckoutInDetails'      => 'Ist PayPal Express aktiv, wird der PayPal Express-Button auf der Artikel-Detailseite angezeigt.',

    'SHOP_MODULE_blOEPayPalECheckoutInMiniBasket'        => 'Express Checkout im Mini-Warenkorb anzeigen',
    'HELP_SHOP_MODULE_blOEPayPalECheckoutInMiniBasket'   => 'Ist PayPal Express aktiv, wird der PayPal Express-Button im Mini-Warenkorb angezeigt.',
);
