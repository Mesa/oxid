[{if $oView->hasPaymentMethodAvailableSubTypes('cc')}]
    <dl id="fcpoCreditcard" style="display:none;">
        <dt>
            <input id="payment_[{$sPaymentID}]" type="radio" name="paymentid" value="[{$sPaymentID}]" [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked[{/if}]>
            <label for="payment_[{$sPaymentID}]"><b>[{$paymentmethod->oxpayments__oxdesc->value}] [{if $paymentmethod->fAddPaymentSum != 0}]([{$paymentmethod->fAddPaymentSum}] [{$currency->sign}])[{/if}]</b></label>
        </dt>
        <dd class="[{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]activePayment[{/if}]">
            <input type="hidden" name="fcpo_cc_type" value="ajax">
            [{foreach from=$aFcPoCCPaymentMetaData item="oFcPoCCPaymentMetaData"}]
                <input type="hidden" name="[{$oFcPoCCPaymentMetaData->sHashName}]" value="[{$oFcPoCCPaymentMetaData->sHashValue}]">
                <input type="hidden" name="[{$oFcPoCCPaymentMetaData->sOperationModeName}]" value="[{$oFcPoCCPaymentMetaData->sOperationModeValue}]">
            [{/foreach}]
            <input type="hidden" name="fcpo_mode_[{$sPaymentID}]" value="[{$paymentmethod->fcpoGetOperationMode()}]">

            <ul class="form">
                <li id="fcpo_cc_error">
                    <div class="oxValidateError" style="display: block;padding: 0;">
                        [{oxmultilang ident="FCPO_ERROR"}]<div id="fcpo_cc_error_content"></div>
                    </div>
                </li>
                <li>
                    <label>[{oxmultilang ident="FCPO_CREDITCARD"}]</label>
                    <select name="dynvalue[fcpo_kktype]" [{if $oView->getMaestroUK()}]onchange="fcCheckType(this); return false;"[{/if}]>
                        [{foreach from=$aFcPoCCPaymentMetaData item="oFcPoCCPaymentMetaData"}]
                            <option value="[{$oFcPoCCPaymentMetaData->sPaymentTag}]" [{if $oFcPoCCPaymentMetaData->blSelected}]selected[{/if}]>[{$oFcPoCCPaymentMetaData->sPaymentName}]</option>
                        [{/foreach}]
                    </select>
                </li>
                <li>
                    <label>[{oxmultilang ident="FCPO_NUMBER"}]</label>
                    <input autocomplete="off" type="text" class="payment_text" size="20" maxlength="64" name="dynvalue[fcpo_kknumber]" value="[{$dynvalue.fcpo_kknumber}]">
                    <div id="fcpo_cc_number_invalid" class="fcpo_check_error">
                        <p class="oxValidateError" style="display: block;">
                            [{oxmultilang ident="FCPO_CC_NUMBER_INVALID"}]
                        </p>
                    </div>
                </li>
                <li>
                    <label>[{oxmultilang ident="FCPO_BANK_ACCOUNT_HOLDER_2"}]</label>
                    <input autocomplete="off" type="text" size="20" maxlength="64" name="dynvalue[fcpo_kkname]" value="[{if $dynvalue.fcpo_kkname}][{$dynvalue.fcpo_kkname}][{else}][{$oxcmp_user->oxuser__oxfname->value}] [{$oxcmp_user->oxuser__oxlname->value}][{/if}]">
                    <br>
                    <div class="note">[{oxmultilang ident="FCPO_IF_DEFERENT_FROM_BILLING_ADDRESS"}]</div>
                </li>
                <li>
                    <label>[{oxmultilang ident="FCPO_VALID_UNTIL"}]</label>
                    <select name="dynvalue[fcpo_kkmonth]">
                        <option [{if $dynvalue.fcpo_kkmonth == "01"}]selected[{/if}]>01</option>
                        <option [{if $dynvalue.fcpo_kkmonth == "02"}]selected[{/if}]>02</option>
                        <option [{if $dynvalue.fcpo_kkmonth == "03"}]selected[{/if}]>03</option>
                        <option [{if $dynvalue.fcpo_kkmonth == "04"}]selected[{/if}]>04</option>
                        <option [{if $dynvalue.fcpo_kkmonth == "05"}]selected[{/if}]>05</option>
                        <option [{if $dynvalue.fcpo_kkmonth == "06"}]selected[{/if}]>06</option>
                        <option [{if $dynvalue.fcpo_kkmonth == "07"}]selected[{/if}]>07</option>
                        <option [{if $dynvalue.fcpo_kkmonth == "08"}]selected[{/if}]>08</option>
                        <option [{if $dynvalue.fcpo_kkmonth == "09"}]selected[{/if}]>09</option>
                        <option [{if $dynvalue.fcpo_kkmonth == "10"}]selected[{/if}]>10</option>
                        <option [{if $dynvalue.fcpo_kkmonth == "11"}]selected[{/if}]>11</option>
                        <option [{if $dynvalue.fcpo_kkmonth == "12"}]selected[{/if}]>12</option>
                    </select>&nbsp;/&nbsp;

                    <select name="dynvalue[fcpo_kkyear]">
                        [{foreach from=$oView->getCreditYears() item=year}]
                            <option [{if $dynvalue.fcpo_kkyear == $year}]selected[{/if}]>[{$year}]</option>
                        [{/foreach}]
                    </select>
                    <div id="fcpo_cc_date_invalid" class="fcpo_check_error">
                        <p class="oxValidateError" style="display: block;">
                            [{oxmultilang ident="FCPO_CC_DATE_INVALID"}]
                        </p>
                    </div>
                </li>
                <li>
                    <label>[{oxmultilang ident="FCPO_CARD_SECURITY_CODE"}]</label>
                    <input autocomplete="off" type="text" class="payment_text" size="20" maxlength="64" name="dynvalue[fcpo_kkpruef]" value="[{$dynvalue.fcpo_kkpruef}]">
                    <div id="fcpo_cc_cvc2_invalid" class="fcpo_check_error">
                        <p class="oxValidateError" style="display: block;">
                            [{oxmultilang ident="FCPO_CC_CVC2_INVALID"}]
                        </p>
                    </div>
                    <div class="clear"></div>
                    <div class="note">[{oxmultilang ident="FCPO_CARD_SECURITY_CODE_DESCRIPTION"}]</div>
                </li>
                [{if $oView->getMaestroUK()}]
                    <li id="fcpo_kkcsn_row" style="display: none;">
                        <label>[{oxmultilang ident="FCPO_CARDSEQUENCENUMBER"}]</label>
                        <input autocomplete="off" type="text" class="payment_text" size="20" maxlength="64" name="dynvalue[fcpo_kkcsn]" value="[{$dynvalue.fcpo_kkcsn}]">
                    </li>
                [{/if}]
            </ul>
            [{block name="checkout_payment_longdesc"}]
                [{if $paymentmethod->oxpayments__oxlongdesc->value}]
                    <div class="desc">
                        [{$paymentmethod->oxpayments__oxlongdesc->getRawValue()}]
                    </div>
                [{/if}]
            [{/block}]
        </dd>
    </dl>
[{/if}]