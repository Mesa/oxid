<?php
/** 
 * PAYONE OXID Connector is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PAYONE OXID Connector is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with PAYONE OXID Connector.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.payone.de
 * @copyright (C) Payone GmbH
 * @version   OXID eShop CE
 */
 
$sLangName  = "Deutsch";
// -------------------------------
// RESOURCE IDENTITFIER = STRING
// -------------------------------
$aLang = array(
'charset'                                       => 'ISO-8859-15',
'FCPO_IBAN_INVALID'                             => 'Bitte geben Sie eine korrekte IBAN ein.',
'FCPO_BIC_INVALID'                              => 'Bitte geben Sie eine korrekte BIC ein.',
'FCPO_BLZ_INVALID'                              => 'Bitte geben Sie eine korrekte Bankleitzahl ein.',
'FCPO_KTONR_INVALID'                            => 'Bitte geben Sie eine korrekte Kontonummer ein.',
'FCPO_ERROR'                                    => 'Es ist ein Fehler aufgetreten:<br>',
'FCPO_ERROR_BLOCKED'                            => 'Kontodaten inkorrekt.',
'FCPO_CC_NUMBER_INVALID'                        => 'Bitte geben Sie eine korrekte Kreditkarten-Nummer ein.',
'FCPO_CC_DATE_INVALID'                          => 'Bitte geben Sie ein korrektes G�ltigkeits-Datum an.',
'FCPO_CC_CVC2_INVALID'                          => 'Bitte geben Sie eine korrekte Pr�fziffer an.',
'fcpo_so_ktonr'                                 => 'Kontonummer:',
'fcpo_so_blz'                                   => 'BLZ:',
'FCPO_MANIPULATION'                             => 'Verdacht auf Manipulation',
'FCPO_REMARK_APPOINTED_MISSING'                 => 'Der Shop hat den Transaktionsstatus APPOINTED nicht erhalten. Bitte pr�fen Sie diese Bezahlung sorgf�ltig!',
'FCPO_THANKYOU_APPOINTED_ERROR'                 => 'Es ist ein Problem im Bezahl-Prozess aufgetreten. Die Bestellung wird von uns gepr&uuml;ft und Sie werden gegebenenfalls kontaktiert.',
'FCPO_CARDSEQUENCENUMBER'                       => 'Card Sequence Number',
'FCPO_ADDRESSCHECK_FAILED1'                     => 'Ihre Adresse konnte nicht verifiziert werden.<br> Grund: "',
'FCPO_ADDRESSCHECK_FAILED2'                     => '"<br>Bitte �berpr�fen Sie Ihre Eingaben.<br>Wenn die Daten dennoch korrekt sind kontaktieren Sie bitte den Kundendienst.',
'FCPO_ADDRESSCHECK_PPB'                         => 'Vor- & Nachname bekannt',
'FCPO_ADDRESSCHECK_PHB'                         => 'Nachname bekannt',
'FCPO_ADDRESSCHECK_PAB'                         => 'Vor- & Nachname nicht bekannt',
'FCPO_ADDRESSCHECK_PKI'                         => 'Mehrdeutigkeit bei Name zu Anschrift',
'FCPO_ADDRESSCHECK_PNZ'                         => 'nicht (mehr) zustellbar',
'FCPO_ADDRESSCHECK_PPV'                         => 'Person verstorben',
'FCPO_ADDRESSCHECK_PPF'                         => 'Adresse postalisch falsch',
'FCPO_ADDRESSCHECK_UKN'                         => 'Unbekannte R�ckgabewerte',
'FCPO_ADDRESSCHECK_PUG'                         => 'Adresse postalisch korrekt aber Geb�ude ist unbekannt',
'FCPO_ADDRESSCHECK_PNZ'                         => 'Kann nicht mehr beliefert werden',
'FCPO_ADDRESSCHECK_PNP'                         => 'Adresse kann nicht gepr�ft werden. Ein falscher Name wurde benutzt',
'FCPO_ONLINE_UEBERWEISUNG_TYPE'                 => 'Typ:',
'FCPO_BANKGROUPTYPE'                            => 'Bankgruppe:',
'FCPO_BANKACCOUNTHOLDER'                        => 'Kontoinhaber:',
'FCPO_VOUCHER'                                  => 'Gutschein',
'FCPO_DISCOUNT'                                 => 'Rabatt',
'FCPO_WRAPPING'                                 => "Geschenkverpackung",
'FCPO_GIFTCARD'                                 => "Gru�karte",
'FCPO_SURCHARGE'                                => 'Aufschlag',
'FCPO_DEDUCTION'                                => 'Abschlag',
'FCPO_PAYMENTTYPE'                              => "Zahlungsart:",
'FCPO_SHIPPINGCOST'                             => "Versandkosten",

'FCPO_BANK_COUNTRY'                             => 'Land der Bank:',
'FCPO_BANK_IBAN'                                => 'IBAN:',
'FCPO_BANK_BIC'                                 => 'BIC:',
'FCPO_BANK_CODE'                                => 'BLZ:',
'FCPO_BANK_ACCOUNT_NUMBER'                      => 'Kto.Nr.:',
'FCPO_BANK_GER_OLD'                             => 'oder bezahlen Sie wie gewohnt mit Ihren bekannten Kontodaten<br>(nur f�r Deutsche Kontoverbindungen).',
'FCPO_CREDITCARD'                               => "Karte:",
'FCPO_CARD_VISA'                                => "Visa",
'FCPO_CARD_MASTERCARD'                          => "Mastercard",
'FCPO_NUMBER'                                   => "Nummer:",
'FCPO_FIRSTNAME'                                => "Vorname:",
'FCPO_LASTNAME'                                 => "Nachname:",
'FCPO_BANK_ACCOUNT_HOLDER_2'                    => "Kontoinhaber:",
'FCPO_IF_DEFERENT_FROM_BILLING_ADDRESS'         => "Falls abweichend von der Rechnungsadresse.",
'FCPO_VALID_UNTIL'                              => "G�ltig bis:",
'FCPO_CARD_SECURITY_CODE'                       => "Pr�fziffer:",
'FCPO_CARD_SECURITY_CODE_DESCRIPTION'           => "Diese befindet sich auf der R�ckseite Ihrer Kreditkarte. Die Pr�fziffer<br>sind die letzten drei Ziffern im Unterschriftsfeld.",
'FCPO_TYPE_OF_PAYMENT'                          => "Zahlungsart",
'FCPO_MIN_ORDER_PRICE'                          => "Mindestbestellwert",
'FCPO_PREVIOUS_STEP'                            => "Zur�ck",
'FCPO_CONTINUE_TO_NEXT_STEP'                    => "Weiter zum n�chsten Schritt",
'FCPO_PAYMENT_INFORMATION'                      => "Bezahlinformation",
'FCPO_PAGE_CHECKOUT_PAYMENT_EMPTY_TEXT'         => '<p>Derzeit ist keine Versandart f�r dieses Land definiert.</p> <p>Wir werden versuchen, Lieferm�glichkeiten zu finden und Sie �ber die Versandkosten informieren.</p>',

'FCPO_EMAIL_BANK_DETAILS'                       => 'Bankdetails',
'FCPO_EMAIL_BANK'                               => 'Bankname:',
'FCPO_EMAIL_ROUTINGNUMBER'                      => 'BLZ:',
'FCPO_EMAIL_ACCOUNTNUMBER'                      => 'Kto.Nr.:',
'FCPO_EMAIL_BIC'                                => 'BIC:',
'FCPO_EMAIL_IBAN'                               => 'IBAN:',
    
'FCPO_KLV_CONFIRM'                              => 'Mit der �bermittlung der f�r die Abwicklung der gew�hlten Klarna Zahlungsmethode und einer Identit�ts- und Bonit�tspr�fung erforderlichen Daten an Klarna bin ich einverstanden. Meine <a target="_blank" style="text-decoration: underline;" href="https://cdn.klarna.com/1.0/shared/content/legal/terms/%s/%s/consent">Einwilligung</a> kann ich jederzeit mit Wirkung f�r die Zukunft widerrufen. Es gelten die AGB des H�ndlers.<br><br>Weitere Informationen zum Rechnungskauf finden Sie in den <a target="_blank" style="text-decoration: underline;" href="https://cdn.klarna.com/1.0/shared/content/legal/terms/%s/%s/invoice?fee=0">Rechnungsbedingungen</a>.',
'FCPO_KLV_TELEPHONENUMBER'                      => 'Telefonnummer',
'FCPO_KLV_TELEPHONENUMBER_INVALID'              => 'Bitte geben Sie eine korrekte Telefonnummer ein.',
'FCPO_KLV_BIRTHDAY'                             => 'Geburtstag',
'FCPO_KLV_BIRTHDAY_INVALID'                     => 'Bitte geben Sie ein korrektes Geburtsdatum ein.',
'FCPO_KLV_ADDINFO'                              => 'Zus. Info',
'FCPO_KLV_ADDINFO_INVALID'                      => 'Bitte f�llen Sie das Feld aus.',
'FCPO_KLV_ADDINFO_DEL'                          => 'Zus. Info Lieferadresse',
'FCPO_KLV_SAL'                                  => 'Anrede',
'FCPO_KLV_PERSONALID'                           => 'Personenkennziffer',
'FCPO_KLV_PERSONALID_INVALID'                   => 'Bitte f�llen Sie das Feld aus.',
'FCPO_KLV_INFO_NEEDED'                          => 'Um den Kauf mit Klarna Rechnung durchf�hren zu k�nnen, ben�tigen wir noch ein paar Angaben von Ihnen.',
'FCPO_KLV_CONFIRMATION_MISSING'                 => 'Sie m�ssen noch Ihr Einverst�ndnis mit der �bermittlung der Daten erkl�ren.',

'FCPO_KLS_CHOOSE_CAMPAIGN'                      => 'Bitte w&auml;hlen Sie die entsprechende Kampagne',
'FCPO_KLS_CAMPAIGN_INVALID'                     => 'Sie m&uuml;ssen eine Kampagne ausw&auml;hlen.',
'FCPO_KLS_NO_CAMPAIGN'                          => 'F&uuml;r Ihre aktuelle Kombination aus Lieferland, Sprache und W&auml;hrung gibt es keine Ratenkauf-Optionen.<br>Bitte w&auml;hlen Sie eine andere Zahlart.',
    
'FCPO_ORDER_MANDATE_HEADER'                     => 'SEPA-Lastschrift',
'FCPO_ORDER_MANDATE_INFOTEXT'                   => 'Damit wir die SEPA-Lastschrift von Ihrem Konto einziehen k�nnen, ben�tigen wir von Ihnen ein SEPA-Lastschriftmandat.',
'FCPO_ORDER_MANDATE_CHECKBOX'                   => 'Ich m�chte das Mandat erteilen<br>(elektronische �bermittlung)',
'FCPO_ORDER_MANDATE_ERROR'                      => 'Sie m�ssen das SEPA-Lastschriftmandat best�tigen.',
    
'FCPO_THANKYOU_PDF_LINK'                        => 'Ihr SEPA-Mandat als PDF',
'FCPO_MANAGEMANDATE_ERROR'                      => 'Es ist ein Problem aufgetreten. Bitte �berpr�fen Sie Ihre Eingaben oder w�hlen Sie eine andere Zahlart.',
    
'FCPO_PAYPALEXPRESS_USER_SECURITY_ERROR'        => 'Bitte loggen Sie sich im Shop ein und f�hren Sie den PayPal Express Checkout nochmal durch. Ihre PayPal-Lieferadresse stimmt nicht mit den im Shop hinterlegten Adressdaten &uuml;berein.',
    
'FCPO_CC_IFRAME_HEADER'                         => 'Bezahlung mit Kreditkarte',
'FCPO_OR'                                       => 'oder',
'FCPO_PAYOLUTION_USTID'                         => 'Umsatzsteueridentifiationsnummer (USt-IdNr.)',
'FCPO_PAYOLUTION_PHONE'                         => 'Telefonnummer',
'FCPO_PAYOLUTION_BIRTHDATE'                     => 'Geburtsdatum',
'FCPO_PAYOLUTION_PRECHECK_FAILED'               => 'Die Transaktion wurde vom Finanzierungs-Dienstleister abgelehnt. Bitte w�hlen Sie eine andere Zahlart',
'FCPO_PAYOLUTION_YEAR'                          => 'Jahr',
'FCPO_PAYOLUTION_MONTH'                         => 'Monat',
'FCPO_PAYOLUTION_DAY'                           => 'Tag',
'FCPO_PAYOLUTION_AGREEMENT_PART_1'              => 'Mit der �bermittlung der f�r die Abwicklung des Rechnungskaufes und einer Identit�tspr�fung und Bonit�tspr�fung erforderlicher Daten an payolution bin ich einverstanden.<br>Meine',
'FCPO_PAYOLUTION_AGREEMENT_PART_2'              => 'kann ich jederzeit mit Wirkung f�r die Zukunft wiederrufen',
'FCPO_PAYOLUTION_AGREE'                         => 'Einwilligung',
'FCPO_PAYOLUTION_EMAIL_CLEARING'                => 'Payolution Referenzcode:',
'FCPO_PAYOLUTION_NOT_AGREED'                    => 'Sie haben die Einwilligung zur �bertragung der erforderlichen Daten an payolution nicht best�tigt.',
'FCPO_PAYOLUTION_SEPA_NOT_AGREED'               => 'Sie haben das SEPA Lastschriftmandat noch nicht erteilt.',
'FCPO_PAYOLUTION_SEPA_AGREEMENT_PART_1'         => 'Hiermit erteile ich das',
'FCPO_PAYOLUTION_SEPA_AGREE'                    => 'SEPA-Lastschriftmandat',
'FCPO_PAYOLUTION_ACCOUNTHOLDER'                 => 'Kontoinhaber',
'FCPO_BANK_IBAN'                                => 'IBAN',
'FCPO_BANK_BIC'                                 => 'BIC',
'FCPO_PAYOLUTION_BANKDATA_INCOMPLETE'           => 'Ihre eingebenen Kontodaten sind nicht vollst�ndig.',
'FCPO_PAYOLUTION_CHECK_INSTALLMENT_AVAILABILITY'=> 'Verf�gbarkeit pr�fen',
'FCPO_PAYOLUTION_INSTALLMENT_SELECTION'         => 'Ratenkaufoptionen',
'FCPO_PAYOLUTION_SELECT_INSTALLMENT'            => 'Bitte w�hlen Sie eine Ratenoption aus',
'FCPO_PAYOLUTION_INSTALLMENT_SUMMARY_AND_ACCOUNT'=> '�bersicht und Kontoinformationen',
'FCPO_PAYOLUTION_PLEASE_CHECK_AVAILABLILITY'    => 'Bitte pr�fen Sie zun�chst die Verf�gbarkeit der m�glichen Ratenzahlungsoptionen',
'FCPO_PAYOLUTION_INSTALLMENT_PER_MONTH'         => 'pro Monat',
'FCPO_PAYOLUTION_INSTALLMENT_RATES'             => 'Raten',
'FCPO_PAYOLUTION_INSTALLMENT_RATE'              => 'Rate',
'FCPO_PAYOLUTION_INSTALLMENT_MONTHLY_RATES'     => 'Monatliche Raten',
'FCPO_PAYOLUTION_INSTALLMENT_INTEREST_RATE'     => 'Nominalzins',
'FCPO_PAYOLUTION_INSTALLMENT_EFF_INTEREST_RATE' => 'Effektivzins',
'FCPO_PAYOLUTION_INSTALLMENT_DUE_AT'            => 'f&auml;llig am',
'FCPO_PAYOLUTION_INSTALLMENT_DOWNLOAD_DRAFT'    => 'Vertragsentwurf herunterladen',
'FCPO_PAYOLUTION_INSTALLMENTS_NUMBER'           => 'Anzahl m�gl. Ratenzahlungen',
'FCPO_PAYOLUTION_INSTALLMENT_FINANCING_AMOUNT'  => 'Zu finanzierender Betrag',
'FCPO_PAYOLUTION_INSTALLMENT_FINANCING_SUM'     => 'Total',
'FCPO_PAYOLUTION_INSTALLMENT_NOT_YET_SELECTED'  => 'Bitte ausw&auml;hlen',
'FCPO_PAYOLUTION_NO_INSTALLMENT_SELECTED'       => 'Sie haben keine Ratenkaufoption ausgew�hlt',
'FCPO_PAYOLUTION_NO_USTID'                      => 'Bei Firmenbestellungen ist die Angabe der Umsatzsteueridentifiationsnummer (USt-IdNr.) notwendig',
'FCPO_RATEPAY_FON'                              => 'Telefonnummer',
'FCPO_RATEPAY_BIRTHDATE'                        => 'Geburtsdatum',
'FCPO_RATEPAY_USTID'                            => 'Umsatzsteueridentifiationsnummer (USt-IdNr.)',
'FCPO_RATEPAY_NO_USTID'                         => 'Bei Firmenbestellungen ist die Angabe der Umsatzsteueridentifiationsnummer (USt-IdNr.) notwendig',
'FCPO_RATEPAY_NO_SUFFICIENT_DATA'               => 'Es fehlen einige pers�nliche Angaben zu Ihrer Person. Bitte f�llen Sie die eingeblendeten Felder aus.',
);

/*
[{oxmultilang ident="GENERAL_YOUWANTTODELETE"}]
*/
