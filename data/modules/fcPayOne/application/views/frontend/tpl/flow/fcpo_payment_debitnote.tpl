<div class="well well-sm">
    <dl>
        <dt>
            <input id="payment_[{$sPaymentID}]" type="radio" name="paymentid" value="[{$sPaymentID}]" [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked[{/if}]>
            <label for="payment_[{$sPaymentID}]"><b>[{$paymentmethod->oxpayments__oxdesc->value}] [{if $paymentmethod->fAddPaymentSum != 0}]([{$paymentmethod->fAddPaymentSum}] [{$currency->sign}])[{/if}]</b></label>
        </dt>
        <dd class="[{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]activePayment[{/if}]">
            <input type="hidden" name="fcpo_mode_[{$sPaymentID}]" value="[{$paymentmethod->fcpoGetOperationMode()}]">
            <div class="form-group" id="fcpo_elv_error">
                <div class="col-lg-9">
                    <span class="help-block">
                        <ul role="alert" class="list-unstyled text-danger">
                            <li>[{oxmultilang ident="FCPO_ERROR"}]<div id="fcpo_elv_error_content"></div></li>
                        </ul>
                    </span>
                </div>
            </div>
            <div class="form-group" id="fcpo_elv_error_blocked">
                <div class="col-lg-9">  
                    <span class="help-block">
                        <ul role="alert" class="list-unstyled text-danger">
                            <li>
                                [{oxmultilang ident="FCPO_ERROR"}]
                                <div>[{oxmultilang ident="FCPO_ERROR_BLOCKED"}]</div>
                            </li>
                        </ul>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <label class="req control-label col-lg-3">[{oxmultilang ident="FCPO_BANK_COUNTRY"}]</label>
                <div class="col-lg-9">
                    <select name="dynvalue[fcpo_elv_country]" onchange="fcCheckDebitCountry(this);return false;" class="form-control selectpicker" required="required">
                        [{foreach from=$oView->fcpoGetDebitCountries() key=sCountryId item=sCountry}]
                            <option value="[{$sCountryId}]" [{if $dynvalue.fcpo_elv_country == $sCountryId}]selected[{/if}]>[{$sCountry}]</option>
                        [{/foreach}]
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="req control-label col-lg-3">[{oxmultilang ident="FCPO_BANK_IBAN"}]</label>
                <div class="col-lg-9">
                    <input class="form-control" autocomplete="off" type="text" size="20" maxlength="64" name="dynvalue[fcpo_elv_iban]" value="[{$dynvalue.fcpo_elv_iban}]" onkeyup="fcHandleDebitInputs('[{$oView->fcpoGetBICMandatory()}]]');return false;">
                    <div id="fcpo_elv_iban_invalid" class="fcpo_check_error">
                        <span class="help-block">
                            <ul role="alert" class="list-unstyled text-danger">
                                <li>[{oxmultilang ident="FCPO_IBAN_INVALID"}]</li>
                            </ul>
                        </span>
                    </div>
                </div>
            </div>
            [{if $oView->getConfigParam('blFCPODebitBICMandatory')}]
                <div class="form-group">
                    <label class="req control-label col-lg-3">[{oxmultilang ident="FCPO_BANK_BIC"}]</label>
                    <div class="col-lg-9">
                        <input class="form-control" autocomplete="off" type="text" size="20" maxlength="64" name="dynvalue[fcpo_elv_bic]" value="[{$dynvalue.fcpo_elv_bic}]" onkeyup="fcHandleDebitInputs('[{$oView->fcpoGetBICMandatory()}]]');return false;">
                        <div id="fcpo_elv_bic_invalid" class="fcpo_check_error">
                            <span class="help-block">
                                <ul role="alert" class="list-unstyled text-danger">
                                    <li>[{oxmultilang ident="FCPO_BIC_INVALID"}]</li>
                                </ul>
                            </span>
                        </div>
                    </div>
                </div>
            [{/if}]
            [{if $oView->fcpoShowOldDebitFields()}]
                <div id="fcpo_elv_ktonr" style="display: none;">
                    <div class="form-group">
                        <div class="col-lg-9 col-lg-offset-3">
                            [{oxmultilang ident="FCPO_BANK_GER_OLD"}]
                        </div>
                    </div>
                    <div class="form-group" >
                        <label class="req control-label col-lg-3">[{oxmultilang ident="FCPO_BANK_ACCOUNT_NUMBER"}]</label>
                        <div class="col-lg-9">
                            <input class="form-control" autocomplete="off" type="text" size="20" maxlength="64" name="dynvalue[fcpo_elv_ktonr]" value="[{$dynvalue.fcpo_elv_ktonr}]" onkeyup="fcHandleDebitInputs('[{$oView->fcpoGetBICMandatory()}]]');return false;">
                            <div id="fcpo_elv_ktonr_invalid" class="fcpo_check_error">
                                <span class="help-block">
                                    <ul role="alert" class="list-unstyled text-danger">
                                        <li>[{oxmultilang ident="FCPO_KTONR_INVALID"}]</li>
                                    </ul>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group" id="fcpo_elv_blz" style="display: none;">
                    <label class="req control-label col-lg-3">[{oxmultilang ident="FCPO_BANK_CODE"}]</label>
                    <div class="col-lg-9">
                        <input class="form-control" autocomplete="off" type="text" size="20" maxlength="64" name="dynvalue[fcpo_elv_blz]" value="[{$dynvalue.fcpo_elv_blz}]" onkeyup="fcHandleDebitInputs('[{$oView->fcpoGetBICMandatory()}]]');return false;">
                        <div id="fcpo_elv_blz_invalid" class="fcpo_check_error">
                            <span class="help-block">
                                <ul role="alert" class="list-unstyled text-danger">
                                    <li>[{oxmultilang ident="FCPO_BLZ_INVALID"}]</li>
                                </ul>
                            </span>
                        </div>
                    </div>
                </div>
            [{/if}]
            [{block name="checkout_payment_longdesc"}]
                [{if $paymentmethod->oxpayments__oxlongdesc->value}]
                    <div class="alert alert-info col-lg-offset-3 desc">
                        [{$paymentmethod->oxpayments__oxlongdesc->getRawValue()}]
                    </div>
                [{/if}]
            [{/block}]
        </dd>
    </dl>
</div>