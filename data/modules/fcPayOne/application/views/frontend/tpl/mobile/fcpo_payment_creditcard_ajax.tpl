[{if $oView->hasPaymentMethodAvailableSubTypes('cc')}]
    <div id="paymentOption_[{$sPaymentID}]" class="payment-option [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]active-payment[{/if}]">
        <input id="payment_[{$sPaymentID}]" type="radio" name="paymentid" value="[{$sPaymentID}]" [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked="checked"[{/if}] />
        <input type="hidden" name="fcpo_cc_type" value="ajax">
        [{foreach from=$aFcPoPaymentMetaData item="oFcPoCCPaymentMetaData"}]
            <input type="hidden" name="[{$oFcPoCCPaymentMetaData->sHashName}]" value="[{$oFcPoCCPaymentMetaData->sHashValue}]">
            <input type="hidden" name="[{$oFcPoCCPaymentMetaData->sOperationModeName}]" value="[{$oFcPoCCPaymentMetaData->sOperationModeValue}]">
        [{/foreach}]
        <ul class="form" id="fcpoCreditcard" style="display:none;">
            <li>
                <div class="dropdown">
                    [{* only to track selection within DOM *}]
                    <input type="hidden" id="sFcpoCreditCardSelected" name="dynvalue[fcpo_kktype]" value="V" [{if $oView->getMaestroUK()}]onchange="fcCheckType(this); return false;"[{/if}] />
                    <div class="dropdown-toggle" data-toggle="dropdown" data-target="#">
                        <a id="dLabelFcpoCreditCardSelected" role="button" href="#">
                            <span id="fcpoCreditCardSelected">[{oxmultilang ident="FCPO_CREDITCARD"}]</span>
                            <i class="glyphicon-chevron-down"></i>
                        </a>
                    </div>
                    [{* TODO: <select [{if $oView->getMaestroUK()}]onchange="fcCheckType(this);return false;"[{/if}]> *}]
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabelFcpoCreditCardSelected">
                        [{foreach from=$aFcPoPaymentMetaData item="oFcPoCCPaymentMetaData"}]
                            <li class="dropdown-option"><a tabindex="-1" data-selection-id="[{$oFcPoCCPaymentMetaData->sPaymentTag}]">[{$oFcPoCCPaymentMetaData->sPaymentName}]</a></li>
                            [{/foreach}]
                    </ul>
                    [{if !empty($dynvalue.fcpo_kktype)}]
                        [{oxscript add="$('#sFcpoCreditCardSelected').val('"|cat:$dynvalue.fcpo_kktype|cat:"');"}]
                    [{/if}]
                </div>
            </li>
            <li>
                <input type="number" size="20" maxlength="64" name="dynvalue[fcpo_kknumber]" autocomplete="off" value="[{$dynvalue.fcpo_kknumber}]" placeholder="[{oxmultilang ident="FCPO_NUMBER"}]" />
                <div id="fcpo_cc_number_invalid" class="fcpo_check_error">
                    <p class="validation-error" style="display: block;">
                        [{oxmultilang ident="FCPO_CC_NUMBER_INVALID"}]
                    </p>
                </div>
            </li>
            <li>
                <input type="text" size="20" maxlength="64" name="dynvalue[kkname]" value="[{if $dynvalue.fcpo_kkname}][{$dynvalue.fcpo_kkname}][{else}][{$oxcmp_user->oxuser__oxfname->value}] [{$oxcmp_user->oxuser__oxlname->value}][{/if}]" placeholder="[{oxmultilang ident="FCPO_BANK_ACCOUNT_HOLDER_2"}]" />
                <br>
                <div class="note">[{oxmultilang ident="FCPO_IF_DEFERENT_FROM_BILLING_ADDRESS"}]</div>
            </li>
            <li>
                <label>[{oxmultilang ident="FCPO_VALID_UNTIL"}]</label>
                <div class="cardValidDateWrapper">
                    <div class="card-valid-date-field card-valid-date-month">
                        <div class="dropdown">
                            <input type="hidden" id="sFcpoCardValidDateMonthSelected" name="dynvalue[fcpo_kkmonth]" value="01" />
                            <div class="dropdown-toggle" data-toggle="dropdown" data-target="#">
                                <a id="dLabelFcpoCardValidDateMonthSelected" role="button" href="#">
                                    <span id="fcpoCardValidDateMonthSelected">01</span>
                                    <i class="glyphicon-chevron-down"></i>
                                </a>
                            </div>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="dLabelFcpoCardValidDateMonthSelected">
                                [{section name="iMonth" start=1 loop=13}]
                                    [{assign var=sMonth value=$smarty.section.iMonth.index|string_format:"%02d"}]
                                    <li class="dropdown-option">
                                        <a tabindex="-1" data-selection-id="[{$sMonth}]">[{$sMonth}]</a>
                                    </li>
                                    [{if $dynvalue.fcpo_kkmonth == $sMonth}]
                                        [{oxscript add="$('#sFcpoCardValidDateMonthSelected').val('"|cat:$sMonth|cat:"');"}]
                                    [{/if}]
                                [{/section}]
                            </ul>
                        </div>
                    </div>
                    <div class="card-valid-date-field card-valid-date-divider">/</div>
                    <div class="card-valid-date-field card-valid-date-year">
                        <div class="dropdown">
                            [{assign var=aYear value=$oView->getCreditYears()}]
                            <input type="hidden" id="sFcpoCardValidDateYearSelected" name="dynvalue[fcpo_kkyear]" value="[{$aYear[0]}]" />
                            <div class="dropdown-toggle" data-toggle="dropdown" data-target="#">
                                <a id="dLabelFcpoCardValidDateYearSelected" role="button" href="#">
                                    <span id="fcpoCardValidDateYearSelected"></span>
                                    <i class="glyphicon-chevron-down"></i>
                                </a>
                            </div>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="dLabelFcpoCardValidDateYearSelected">
                                [{foreach from=$aYear item=iYear}]
                                    <li class="dropdown-option">
                                        <a tabindex="-1" data-selection-id="[{$iYear}]">[{$iYear}]</a>
                                    </li>
                                    [{if $dynvalue.fcpo_kkyear == $iYear}]
                                        [{oxscript add="$('#sFcpoCardValidDateYearSelected').val('"|cat:$iYear|cat:"');"}]
                                    [{/if}]
                                [{/foreach}]
                            </ul>
                        </div>
                    </div>
                </div>
                <div id="fcpo_cc_date_invalid" class="fcpo_check_error">
                    <p class="validation-error" style="display: block;">
                        [{oxmultilang ident="FCPO_CC_DATE_INVALID"}]
                    </p>
                </div>
            </li>
            <li>
                <input type="text" size="20" maxlength="64" name="dynvalue[fcpo_kkpruef]" autocomplete="off" value="[{$dynvalue.fcpo_kkpruef}]" placeholder="[{oxmultilang ident="FCPO_CARD_SECURITY_CODE"}]" />
                <div id="fcpo_cc_cvc2_invalid" class="fcpo_check_error">
                    <p class="validation-error" style="display: block;">
                        [{oxmultilang ident="FCPO_CC_CVC2_INVALID"}]
                    </p>
                </div>
                <div class="clear"></div>
                <div class="note">[{oxmultilang ident="FCPO_CARD_SECURITY_CODE_DESCRIPTION"}]</div>
            </li>
            [{if $oView->getMaestroUK()}]
                <li id="fcpo_kkcsn_row" style="display: none;">
                    <input type="number" class="js-oxValidate js-oxValidate_notEmpty" size="20" maxlength="64" name="dynvalue[fcpo_kkcsn]" autocomplete="off" value="[{$dynvalue.fcpo_kkcsn}]" placeholder="[{oxmultilang ident="FCPO_CARDSEQUENCENUMBER"}]" />
                    <p class="validation-error">
                        <span class="js-oxError_notEmpty">[{oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS"}]</span>
                    </p>
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
[{/if}]