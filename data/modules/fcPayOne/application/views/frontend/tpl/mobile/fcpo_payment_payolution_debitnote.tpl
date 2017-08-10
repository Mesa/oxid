<div id="paymentOption_[{$sPaymentID}]" class="payment-option [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]active-payment[{/if}]">
    <input id="payment_[{$sPaymentID}]" type="radio" name="paymentid" value="[{$sPaymentID}]" [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked="checked"[{/if}] />
    <input type="hidden" name="fcpo_mode_[{$sPaymentID}]" value="[{$paymentmethod->fcpoGetOperationMode()}]">
    <link href="[{$oViewConf->fcpoGetModuleCssPath('lightview.css')}]" rel="stylesheet">
    <script src="[{$oViewConf->fcpoGetModuleJsPath('jquery-1.10.1.min.js')}]"></script>
    <script src="[{$oViewConf->fcpoGetModuleJsPath()}]lightview/lightview.js"></script>
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
        [{if $oView->fcpoShowB2C()}]
            <li>
                <input placceholder="[{oxmultilang ident="FCPO_PAYOLUTION_YEAR"}]" type="text" size="5" maxlength="4" name="dynvalue[fcpo_payolution_debitnote_birthdate_year]" value="[{$oView->fcpoGetBirthdayField('year')}]">&nbsp;
                <input placceholder="[{oxmultilang ident="FCPO_PAYOLUTION_MONTH"}]" type="text" size="3" maxlength="2" name="dynvalue[fcpo_payolution_debitnote_birthdate_month]" value="[{$oView->fcpoGetBirthdayField('month')}]">&nbsp;
                <input placceholder="[{oxmultilang ident="FCPO_PAYOLUTION_DAY"}]" type="text" size="3" maxlength="2" name="dynvalue[fcpo_payolution_debitnote_birthdate_day]" value="[{$oView->fcpoGetBirthdayField('day')}]">
            </li>
        [{/if}]
        <li>
            <input type="text" size="20" maxlength="64" name="dynvalue[fcpo_payolution_debitnote_accountholder]" autocomplete="off" value="[{$dynvalue.fcpo_payolution_debitnote_accountholder}]" onkeyup="fcHandleDebitInputs(); return false;" placeholder="[{oxmultilang ident="FCPO_BANK_IBAN"}]" />
        </li>
        <li>
            <input type="text" size="20" maxlength="64" name="dynvalue[fcpo_payolution_debitnote_iban]" autocomplete="off" value="[{$dynvalue.fcpo_payolution_debitnote_iban}]" onkeyup="fcHandleDebitInputs(); return false;" placeholder="[{oxmultilang ident="FCPO_BANK_IBAN"}]" />
            <div id="fcpo_elv_iban_invalid" class="fcpo_check_error">
                <p class="validation-error" style="display: block;">
                    [{oxmultilang ident="FCPO_IBAN_INVALID"}]
                </p>
            </div>
        </li>
        <li>
            <input type="text" size="20" maxlength="64" name="dynvalue[fcpo_payolution_debitnote_bic]" autocomplete="off" value="[{$dynvalue.fcpo_payolution_debitnote_bic}]" onkeyup="fcHandleDebitInputs(); return false;" placeholder="[{oxmultilang ident="FCPO_BANK_BIC"}]" />
            <div id="fcpo_elv_bic_invalid" class="fcpo_check_error">
                <p class="validation-error" style="display: block;">
                    [{oxmultilang ident="FCPO_BIC_INVALID"}]
                </p>
            </div>
        </li>
        <li>
            <input name="dynvalue[fcpo_payolution_debitnote_agreed]" value="agreed" type="checkbox">&nbsp;[{oxmultilang ident="FCPO_PAYOLUTION_AGREEMENT_PART_1"}] <a href='[{$oView->fcpoGetPayolutionAgreementLink()}]' class="lightview fcpoPayolutionAgreeRed" data-lightview-type="iframe" data-lightview-options="width: 800, height: 600, viewport: 'scale',background: { color: '#fff', opacity: 1 },skin: 'light'">[{oxmultilang ident="FCPO_PAYOLUTION_AGREE"}]</a> [{oxmultilang ident="FCPO_PAYOLUTION_AGREEMENT_PART_2"}]
        </li>
        <li>
            <input name="dynvalue[fcpo_payolution_debitnote_sepa_agreed]" value="agreed" type="checkbox">&nbsp;[{oxmultilang ident="FCPO_PAYOLUTION_SEPA_AGREEMENT_PART_1"}] <a href='[{$oView->fcpoGetPayolutionSepaAgreementLink()}]' class="lightview fcpoPayolutionAgreeRed" data-lightview-type="iframe" data-lightview-options="width: 800, height: 600, viewport: 'scale',background: { color: '#fff', opacity: 1 },skin: 'light'">[{oxmultilang ident="FCPO_PAYOLUTION_SEPA_AGREE"}]</a>
        </li>
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