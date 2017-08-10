[{include file="headitem.tpl" title="SYSREQ_MAIN_TITLE"|oxmultilangassign}]

[{ if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form autocomplete="off" name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="fcpayone_log">
</form>

[{ if $oxid == '-1' }]

[{oxmultilang ident="FCPO_NO_TRANSACTION"}]

[{ else }]

    <table style="border: 1px solid #C8C8C8;">
        <tr>
            <td class="listitem" >
                TXACTION
            </td>
            <td class="listitem">
                [{ $edit->fcpotransactionstatus__fcpo_txaction->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem2" >
                PORTALID
            </td>
            <td class="listitem2">
                [{ $edit->fcpotransactionstatus__fcpo_portalid->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem" >
                AID
            </td>
            <td class="listitem">
                [{ $edit->fcpotransactionstatus__fcpo_aid->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem2" >
                CLEARINGTYPE
            </td>
            <td class="listitem2">
                [{ $edit->fcpotransactionstatus__fcpo_clearingtype->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem" >
                TXTIME
            </td>
            <td class="listitem">
                [{ $edit->fcpotransactionstatus__fcpo_txtime->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem2" >
                CURRENCY
            </td>
            <td class="listitem2">
                [{ $edit->fcpotransactionstatus__fcpo_currency->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem" >
                USERID
            </td>
            <td class="listitem">
                [{ $edit->fcpotransactionstatus__fcpo_userid->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem2" >
                ACCESSNAME
            </td>
            <td class="listitem2">
                [{ $edit->fcpotransactionstatus__fcpo_accessname->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem" >
                ACCESSCODE
            </td>
            <td class="listitem">
                [{ $edit->fcpotransactionstatus__fcpo_accesscode->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem2" >
                MODE
            </td>
            <td class="listitem2">
                [{ $edit->fcpotransactionstatus__fcpo_mode->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem" >
                PRICE
            </td>
            <td class="listitem">
                [{ $edit->fcpotransactionstatus__fcpo_price->value }]
            </td>
        </tr>        
        
        <tr>
            <td class="listitem2" >
                CLEARING_BANKACCOUNTHOLDER
            </td>
            <td class="listitem2">
                [{ $edit->fcpotransactionstatus__fcpo_clearing_bankaccountholder->value }]
            </td>
        </tr>
        
        <tr>
            <td class="listitem" >
                CLEARING_BANKACCOUNT
            </td>
            <td class="listitem">
                [{ $edit->fcpotransactionstatus__fcpo_clearing_bankaccount->value }]
            </td>
        </tr>
        
        <tr>
            <td class="listitem2" >
                CLEARING_BANKCODE
            </td>
            <td class="listitem2">
                [{ $edit->fcpotransactionstatus__fcpo_clearing_bankcode->value }]
            </td>
        </tr>
        
        <tr>
            <td class="listitem" >
                CLEARING_BANKNAME
            </td>
            <td class="listitem">
                [{ $edit->fcpotransactionstatus__fcpo_clearing_bankname->value }]
            </td>
        </tr>
        
        <tr>
            <td class="listitem2" >
                CLEARING_BANKBIC
            </td>
            <td class="listitem2">
                [{ $edit->fcpotransactionstatus__fcpo_clearing_bankbic->value }]
            </td>
        </tr>
        
        <tr>
            <td class="listitem" >
                CLEARING_BANKIBAN
            </td>
            <td class="listitem">
                [{ $edit->fcpotransactionstatus__fcpo_clearing_bankiban->value }]
            </td>
        </tr>
        
        <tr>
            <td class="listitem2" >
                CLEARING_LEGALNOTE
            </td>
            <td class="listitem2">
                [{ $edit->fcpotransactionstatus__fcpo_clearing_legalnote->value }]
            </td>
        </tr>
        
        <tr>
            <td class="listitem" >
                CLEARING_DUEDATE
            </td>
            <td class="listitem">
                [{ $edit->fcpotransactionstatus__fcpo_clearing_duedate->value }]
            </td>
        </tr>
        
        <tr>
            <td class="listitem2" >
                CLEARING_REFERENCE
            </td>
            <td class="listitem2">
                [{ $edit->fcpotransactionstatus__fcpo_clearing_reference->value }]
            </td>
        </tr>
        
        <tr>
            <td class="listitem" >
                CLEARING_INSTRUCTIONNOTE
            </td>
            <td class="listitem">
                [{ $edit->fcpotransactionstatus__fcpo_clearing_instructionnote->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem2" >
                TXID
            </td>
            <td class="listitem2">
                [{ $edit->fcpotransactionstatus__fcpo_txid->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem" >
                REFERENCE
            </td>
            <td class="listitem">
                [{ $edit->fcpotransactionstatus__fcpo_reference->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem2" >
                SEQUENCENUMBER
            </td>
            <td class="listitem2">
                [{ $edit->fcpotransactionstatus__fcpo_sequencenumber->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem" >
                COMPANY
            </td>
            <td class="listitem">
                [{ $edit->fcpotransactionstatus__fcpo_company->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem2" >
                FIRSTNAME
            </td>
            <td class="listitem2">
                [{ $edit->fcpotransactionstatus__fcpo_firstname->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem" >
                LASTNAME
            </td>
            <td class="listitem">
                [{ $edit->fcpotransactionstatus__fcpo_lastname->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem2" >
                STREET
            </td>
            <td class="listitem2">
                [{ $edit->fcpotransactionstatus__fcpo_street->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem" >
                ZIP
            </td>
            <td class="listitem">
                [{ $edit->fcpotransactionstatus__fcpo_zip->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem2" >
                CITY
            </td>
            <td class="listitem2">
                [{ $edit->fcpotransactionstatus__fcpo_city->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem" >
                EMAIL
            </td>
            <td class="listitem">
                [{ $edit->fcpotransactionstatus__fcpo_email->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem2" >
                COUNTRY
            </td>
            <td class="listitem2">
                [{ $edit->fcpotransactionstatus__fcpo_country->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem" >
                SHIPPING_COMPANY
            </td>
            <td class="listitem">
                [{ $edit->fcpotransactionstatus__fcpo_shipping_company->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem2" >
                SHIPPING_FIRSTNAME
            </td>
            <td class="listitem2">
                [{ $edit->fcpotransactionstatus__fcpo_shipping_firstname->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem" >
                SHIPPING_LASTNAME
            </td>
            <td class="listitem">
                [{ $edit->fcpotransactionstatus__fcpo_shipping_lastname->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem2" >
                SHIPPING_STREET
            </td>
            <td class="listitem2">
                [{ $edit->fcpotransactionstatus__fcpo_shipping_street->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem" >
                SHIPPING_ZIP
            </td>
            <td class="listitem">
                [{ $edit->fcpotransactionstatus__fcpo_shipping_zip->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem2" >
                SHIPPING_CITY
            </td>
            <td class="listitem2">
                [{ $edit->fcpotransactionstatus__fcpo_shipping_city->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem" >
                SHIPPING_COUNTRY
            </td>
            <td class="listitem">
                [{ $edit->fcpotransactionstatus__fcpo_shipping_country->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem2" >
                BANKCOUNTRY
            </td>
            <td class="listitem2">
                [{ $edit->fcpotransactionstatus__fcpo_bankcountry->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem" >
                BANKACCOUNT
            </td>
            <td class="listitem">
                [{ $edit->fcpotransactionstatus__fcpo_bankaccount->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem2" >
                BANKCODE
            </td>
            <td class="listitem2">
                [{ $edit->fcpotransactionstatus__fcpo_bankcode->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem" >
                BANKACCOUNTHOLDER
            </td>
            <td class="listitem">
                [{ $edit->fcpotransactionstatus__fcpo_bankaccountholder->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem2" >
                CARDEXPIREDATE
            </td>
            <td class="listitem2">
                [{ $edit->fcpotransactionstatus__fcpo_cardexpiredate->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem" >
                CARDTYPE
            </td>
            <td class="listitem">
                [{ $edit->fcpotransactionstatus__fcpo_cardtype->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem2" >
                CARDPAN
            </td>
            <td class="listitem2">
                [{ $edit->fcpotransactionstatus__fcpo_cardpan->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem" >
                CUSTOMERID
            </td>
            <td class="listitem">
                [{ $edit->fcpotransactionstatus__fcpo_customerid->value }]
            </td>
        </tr>

        <tr>
            <td class="listitem2" >
                BALANCE
            </td>
            <td class="listitem2">
                [{ $edit->fcpotransactionstatus__fcpo_balance->value|number_format:2:',':'' }]
            </td>
        </tr>

        <tr>
            <td class="listitem" >
                RECEIVABLE
            </td>
            <td class="listitem">
                [{ $edit->fcpotransactionstatus__fcpo_receivable->value|number_format:2:',':'' }]
            </td>
        </tr>
    </table>

[{/if}]

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]