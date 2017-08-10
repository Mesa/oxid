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
        <div class="fcRow">
            <div class="fcCol fcCol-1">
                <div class="content">
                    <p id="payolution_installment_availibility_headline" class="payolution_installment_box_headline">1. [{oxmultilang ident="FCPO_PAYOLUTION_CHECK_INSTALLMENT_AVAILABILITY"}]</p>
                    <p id="payolution_installment_availibility_subtitle" class="payolution_installment_box_subtitle">[{oxmultilang ident="FCPO_PAYOLUTION_BIRTHDATE"}]</p>
                    <p id="payolution_installment_availibility_body" class="payolution_installment_box_body">
                    <p id="payolution_installment_availibility_body" class="payolution_installment_box_body">
                        <select name="dynvalue[fcpo_payolution_installment_birthdate_day]">
                            [{foreach from=$oView->fcpoGetDayRange() item='sDay'}]
                                <option value="[{$sDay}]" [{if $sDay == $oView->fcpoGetBirthdayField('day')}]selected[{/if}]>[{$sDay}]</option>
                            [{/foreach}]
                        </select>
                        &nbsp;
                        <select name="dynvalue[fcpo_payolution_installment_birthdate_month]">
                            [{foreach from=$oView->fcpoGetMonthRange() item='sMonth'}]
                                <option value="[{$sMonth}]" [{if $sMonth == $oView->fcpoGetBirthdayField('month')}]selected[{/if}]>[{$sMonth}]</option>
                            [{/foreach}]
                        </select>
                        &nbsp;
                        <select name="dynvalue[fcpo_payolution_installment_birthdate_year]">
                            [{foreach from=$oView->fcpoGetYearRange() item='sYear'}]
                                <option value="[{$sYear}]" [{if $sYear == $oView->fcpoGetBirthdayField('year')}]selected[{/if}]>[{$sYear}]</option>
                            [{/foreach}]
                        </select>
                        <br>
                        <input name="dynvalue[fcpo_payolution_installment_agreed]" value="agreed" type="checkbox">&nbsp;[{oxmultilang ident="FCPO_PAYOLUTION_AGREEMENT_PART_1"}] <a href='[{$oView->fcpoGetPayolutionAgreementLink()}]' class="lightview fcpoPayolutionAgreeRed" data-lightview-type="iframe" data-lightview-options="width: 800, height: 600, viewport: 'scale',background: { color: '#fff', opacity: 1 },skin: 'light'">[{oxmultilang ident="FCPO_PAYOLUTION_AGREE"}]</a> [{oxmultilang ident="FCPO_PAYOLUTION_AGREEMENT_PART_2"}]
                    </p>
                </div>
                <input type="button" id="payolution_installment_check_availability" class="fcBTN-bot" value="[{oxmultilang ident="FCPO_PAYOLUTION_CHECK_INSTALLMENT_AVAILABILITY"}]">
            </div>
            <div class="fcCol fcCol-2">
                <div id="payolution_installment_calculation_selection">
                    <div class="content">
                        <p id="payolution_installment_calculation_headline" class="payolution_installment_box_headline">2. [{oxmultilang ident="FCPO_PAYOLUTION_INSTALLMENT_SELECTION"}]</p>
                        <p id="payolution_installment_calculation_headline" class="payolution_installment_box_subtitle">[{oxmultilang ident="FCPO_PAYOLUTION_SELECT_INSTALLMENT"}]</p>
                        <p id="payolution_installment_calculation_headline" class="payolution_installment_box_body">
                        <p id="payolution_installment_calculation_greeter" class="payolution_message_notifiation">[{oxmultilang ident="FCPO_PAYOLUTION_PLEASE_CHECK_AVAILABLILITY"}]</p>
                    </div>
                </div>
            </div>
            <div class="fcCol fcCol-3">
                <div class="content">
                    <p id="payolution_installment_overview_headline" class="payolution_installment_box_headline">3. [{oxmultilang ident="FCPO_PAYOLUTION_INSTALLMENT_SUMMARY_AND_ACCOUNT"}]</p>
                    <p id="payolution_installment_overview_headline" class="payolution_installment_box_body">
                        <div id="payolution_installment_overview_account_info">
                            <input name="dynvalue[fcpo_payolution_installment_index]" type="hidden" id="payolution_selected_installment_index" value="">
                            <table>
                                <tr>
                                    <td>[{oxmultilang ident="FCPO_PAYOLUTION_INSTALLMENTS_NUMBER"}]</td>
                                    <td><span id="payolution_sum_number_installments"></span></td>
                                </tr>
                                <tr>
                                    <td>[{oxmultilang ident="FCPO_PAYOLUTION_INSTALLMENT_FINANCING_AMOUNT"}]</td>
                                    <td>[{$oView->fcpoGetBasketSum()}] [{$currency->sign}]</td>
                                </tr>
                                <tr>
                                    <td>[{oxmultilang ident="FCPO_PAYOLUTION_INSTALLMENT_FINANCING_SUM"}]</td>
                                    <td><span id="payolution_financing_sum">[{oxmultilang ident="FCPO_PAYOLUTION_INSTALLMENT_NOT_YET_SELECTED"}]</span> [{$currency->sign}]</td>
                                </tr>
                                <tr>
                                    <td>[{oxmultilang ident="FCPO_PAYOLUTION_INSTALLMENT_INTEREST_RATE"}]</td>
                                    <td><span id="payolution_sum_interest_rate">[{oxmultilang ident="FCPO_PAYOLUTION_INSTALLMENT_NOT_YET_SELECTED"}]</span> %</td>
                                </tr>
                                <tr>
                                    <td>[{oxmultilang ident="FCPO_PAYOLUTION_INSTALLMENT_EFF_INTEREST_RATE"}]</td>
                                    <td><span id="payolution_sum_eff_interest_rate">[{oxmultilang ident="FCPO_PAYOLUTION_INSTALLMENT_NOT_YET_SELECTED"}]</span> %</td>
                                </tr>
                                <tr>
                                    <td><strong>[{oxmultilang ident="FCPO_PAYOLUTION_INSTALLMENT_MONTHLY_RATES"}]</strong></td>
                                    <td><span id="payolution_sum_monthly_rate" class="fcpoPayolutionAgreeRed">[{oxmultilang ident="FCPO_PAYOLUTION_INSTALLMENT_NOT_YET_SELECTED"}]</span> [{$currency->sign}]</td>
                                </tr>
                            </table>
                        </div>
                        <ul class="form">
                            <li id="fcpo_elv_error">
                                <div class="oxValidateError" style="display: block;padding: 0;">
                                    [{oxmultilang ident="FCPO_ERROR"}]<div id="fcpo_elv_error_content"></div>
                                </div>
                            </li>
                            <li>
                                <label>[{oxmultilang ident="FCPO_PAYOLUTION_ACCOUNTHOLDER"}]</label>
                                <input autocomplete="off" type="text" size="20" maxlength="64" name="dynvalue[fcpo_payolution_installment_accountholder]" value="[{$dynvalue.fcpo_payolution_installment_accountholder}]" onkeyup="fcHandleDebitInputs();return false;">
                            </li>
                            <li>
                                <label>[{oxmultilang ident="FCPO_BANK_IBAN"}]</label>
                                <input autocomplete="off" type="text" size="20" maxlength="64" name="dynvalue[fcpo_payolution_installment_iban]" value="[{$dynvalue.fcpo_payolution_installment_iban}]" onkeyup="fcHandleDebitInputs();return false;">
                                <div id="fcpo_payolution_iban_invalid" class="fcpo_check_error">
                                    <p class="oxValidateError" style="display: block;">
                                        [{oxmultilang ident="FCPO_IBAN_INVALID"}]
                                    </p>
                                </div>
                            </li>
                            <li>
                                <label>[{oxmultilang ident="FCPO_BANK_BIC"}]</label>
                                <input autocomplete="off" type="text" size="20" maxlength="64" name="dynvalue[fcpo_payolution_installment_bic]" value="[{$dynvalue.fcpo_payolution_installment_bic}]" onkeyup="fcHandleDebitInputs();return false;">
                                <div id="fcpo_payolution_bic_invalid" class="fcpo_check_error">
                                    <p class="oxValidateError" style="display: block;">
                                        [{oxmultilang ident="FCPO_BIC_INVALID"}]
                                    </p>
                                </div>
                            </li>
                            <li>

                            </li>
                        </ul>
                    </p>
                </div>
            </div>
        </div>
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