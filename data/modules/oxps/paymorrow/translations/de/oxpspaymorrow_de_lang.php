<?php
/**
 * This file is part of OXID eSales Paymorrow module.
 *
 * OXID eSales Paymorrow module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales eVAT module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales eVAT module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2015
 */

$sLangName = "Deutsch";

$aLang = array(
    "charset"                                        => "ISO-8859-15", // Supports DE chars like: ä, ü, ö, etc.

    'PAYMORROW_PAYMENT_METHOD_NAME_INVOICE'          => 'Rechnungskauf',
    'PAYMORROW_PAYMENT_METHOD_NAME_DIRECT_DEBIT'     => 'Lastschriftverfahren',

    'PAYMORROW_PAYMENT_NO_JAVASCRIPT'                => 'Um diese Zahlungsart zu nutzen, muss JavaScript im Browser aktiviert sein.',

    'PAYMORROW_GENERAL_ERROR'                        => 'Es ist ein Fehler aufgetreten. Bitte wiederholen Sie den Vorgang.',
    'PAYMORROW_ACCEPT_CONDITIONS_ERROR'              => 'Bitte akzeptieren Sie die Datenschutzbestimmungen der Paymorrow GmbH.',
    'PAYMORROW_SELECT_GENDER_ERROR'                  => 'Sie haben keine Anrede ausgewählt.',
    'PAYMORROW_DATE_OF_BIRTH_ERROR'                  => 'Sie haben kein Geburtsdatum angegeben.',
    'PAYMORROW_MOBILE_NUMBER_ERROR'                  => 'Sie haben keine Festnetz- oder Mobilnummer eingegeben.',

    // Custom
    'PAYMORROW_ORDER_DATA_COLLECTION_FAILED'         => 'Die Bestelldatenerfassung ist gescheitert',
    'PAYMORROW_ORDER_SAVING_TEMPORARY_ORDER_FAILED'  => 'Das Speichern der temporären Bestellung ist gescheitert.',

    // Email
    'EMAIL_ORDER_CUST_HTML_PAYMENTMETHOD'            => 'Zahlungsart:',
    'PAYMORROW_EMAIL_ORDER_CUST_HTML_BANK'           => 'Bank:',
    'PAYMORROW_EMAIL_ORDER_CUST_HTML_IBAN'           => 'IBAN:',
    'PAYMORROW_EMAIL_ORDER_CUST_HTML_BIC'            => 'BIC:',
    'PAYMORROW_EMAIL_ORDER_CUST_HTML_REFERENCE_LINE' => 'Verwendungszweck:',
    'PAYMORROW_EMAIL_ORDER_CUST_HTML_ORDER_ID'       => 'BE',
    'PAYMORROW_EMAIL_ORDER_CUST_HTML_CUSTOMER_NR'    => 'KD',
);
