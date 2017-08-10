<div id="paymentOption_[{$sPaymentID}]" class="payment-option [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]active-payment[{/if}]">
    <input id="payment_[{$sPaymentID}]" type="radio" name="paymentid" value="[{$sPaymentID}]" [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked="checked"[{/if}] />
    <input type="hidden" name="fcpo_mode_[{$sPaymentID}]" value="[{$paymentmethod->fcpoGetOperationMode()}]">
    <ul class="form">
        <li id="fcpo_elv_error">
            <div class="validation-error" style="display: block;padding: 0;">
                [{oxmultilang ident="FCPO_ERROR"}]<div id="fcpo_elv_error_content"></div>
            </div>
        </li>
        <li id="fcpo_elv_error_blocked">
            <div class="validation-error" style="display: block;padding: 0;">
                [{oxmultilang ident="FCPO_ERROR"}]
                <div>[{oxmultilang ident="FCPO_ERROR_BLOCKED"}]</div>
            </div>
        </li>
        <li>
            <div class="dropdown">
                [{assign var="sFirstCountry" value=""}]
                [{foreach from=$oView->fcpoGetDebitCountries() key=sCountryId item=sCountry}]
                    [{if $sFirstCountry == ""}]
                        [{assign var="sFirstCountry" value=$sCountryId}]
                    [{/if}]
                [{/foreach}]
                <input type="hidden" id="sFcpoDebitNoteCountrySelected" name="dynvalue[fcpo_elv_country]" value="[{$sFirstCountry}]" onchange="fcCheckDebitCountry(this);return false;" />
                <div class="dropdown-toggle" data-toggle="dropdown" data-target="#">
                    <a id="dLabelFcpoDebitNoteCountrySelected" role="button" href="#">
                        <span id="fcpoDebitNoteCountrySelected">[{oxmultilang ident="FCPO_BANK_COUNTRY"}]</span>
                        <i class="glyphicon-chevron-down"></i>
                    </a>
                </div>
                <ul class="dropdown-menu" role="menu" aria-labelledby="dLabelFcpoDebitNoteCountrySelected">
                    [{foreach from=$oView->fcpoGetDebitCountries() key=sCountryId item=sCountry}]
                        <li class="dropdown-option"><a tabindex="-1" data-selection-id="[{$sCountryId}]">[{$sCountry}]</a></li>
                        [{/foreach}]
                </ul>
                [{if !empty($dynvalue.fcpo_elv_country)}]
                    [{oxscript add="$('#sFcpoDebitNoteCountrySelected').val('"|cat:$dynvalue.fcpo_elv_country|cat:"');"}]
                [{/if}]
            </div>
        </li>
        <li>
            <input type="text" size="20" maxlength="64" name="dynvalue[fcpo_elv_iban]" autocomplete="off" value="[{$dynvalue.fcpo_elv_iban}]" onkeyup="fcHandleDebitInputs(); return false;" placeholder="[{oxmultilang ident="FCPO_BANK_IBAN"}]" />
            <div id="fcpo_elv_iban_invalid" class="fcpo_check_error">
                <p class="validation-error" style="display: block;">
                    [{oxmultilang ident="FCPO_IBAN_INVALID"}]
                </p>
            </div>
        </li>
        <li>
            <input type="text" size="20" maxlength="64" name="dynvalue[fcpo_elv_bic]" autocomplete="off" value="[{$dynvalue.fcpo_elv_bic}]" onkeyup="fcHandleDebitInputs(); return false;" placeholder="[{oxmultilang ident="FCPO_BANK_BIC"}]" />
            <div id="fcpo_elv_bic_invalid" class="fcpo_check_error">
                <p class="validation-error" style="display: block;">
                    [{oxmultilang ident="FCPO_BIC_INVALID"}]
                </p>
            </div>
        </li>
        [{if $oView->fcpoShowOldDebitFields()}]
            <li id="fcpo_elv_ktonr" style="display: none;">
                <div style="margin-top: 20px;margin-bottom:10px;">[{oxmultilang ident="FCPO_BANK_GER_OLD"}]</div>
                <input type="text" size="20" maxlength="64" name="dynvalue[fcpo_elv_ktonr]" autocomplete="off" value="[{$dynvalue.fcpo_elv_ktonr}]" onkeyup="fcHandleDebitInputs(); return false;" placeholder="[{oxmultilang ident="FCPO_BANK_ACCOUNT_NUMBER"}]" />
                <div id="fcpo_elv_ktonr_invalid" class="fcpo_check_error">
                    <p class="validation-error" style="display: block;">
                        [{oxmultilang ident="FCPO_KTONR_INVALID"}]
                    </p>
                </div>
            </li>
            <li id="fcpo_elv_blz" style="display: none;">
                <input type="text" size="20" maxlength="64" name="dynvalue[fcpo_elv_blz]" autocomplete="off" value="[{$dynvalue.fcpo_elv_blz}]" onkeyup="fcHandleDebitInputs(); return false;" placeholder="[{oxmultilang ident="FCPO_BANK_CODE"}]" />
                <div id="fcpo_elv_blz_invalid" class="fcpo_check_error">
                    <p class="validation-error" style="display: block;">
                        [{oxmultilang ident="FCPO_BLZ_INVALID"}]
                    </p>
                </div>
            </li>
        [{/if}]
        [{block name="checkout_payment_longdesc"}]
            [{if $paymentmethod->oxpayments__oxlongdesc->value}]
                <li>
                    <div class="payment-desc">
                        [{$paymentmethod->oxpayments__oxlongdesc->getRawValue()}]
                    </div>
                </li>
            [{/if}]
        [{/block}]
    </ul>
</div>
[{oxscript add="$('#paymentOption_$sPaymentID').find('.dropdown').oxDropDown();"}]