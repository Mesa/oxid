[{if $oView->hasPaymentMethodAvailableSubTypes('cc')}]
    <div id="paymentOption_[{$sPaymentID}]" class="payment-option [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]active-payment[{/if}]">
        <input id="payment_[{$sPaymentID}]" type="radio" name="paymentid" value="[{$sPaymentID}]" [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked="checked"[{/if}] />
        <script type="text/javascript" src="[{$oViewConf->fcpoGetHostedPayoneJs()}]"></script>
        <input type="hidden" name="dynvalue[fcpo_kknumber]" value="">
        <input type="hidden" name="fcpo_cc_type" value="hosted">
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
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabelFcpoCreditCardSelected">
                        [{foreach from=$aFcPoPaymentMetaData item="oFcPoCCPaymentMetaData"}]
                            <li class="dropdown-option"><a tabindex="-1" data-selection-id="[{$oFcPoCCPaymentMetaData->sPaymentTag}]">[{$oFcPoCCPaymentMetaData->sPaymentName}]</a></li>
                            [{/foreach}]
                    </ul>
                    [{if !empty($dynvalue.fcpo_kktype)}]
                        [{oxscript add="$('#sFcpoCreditCardSelected').val('"|cat:$dynvalue.fcpo_kktype|cat:"');"}]
                    [{/if}]
                </div>
                [{foreach from=$aFcPoPaymentMetaData item="oFcPoCCPaymentMetaData"}]
                    <input type="hidden" name="[{$oFcPoCCPaymentMetaData->sHashName}]" value="[{$oFcPoCCPaymentMetaData->sHashValue}]">
                    <input type="hidden" name="[{$oFcPoCCPaymentMetaData->sOperationModeName}]" value="[{$oFcPoCCPaymentMetaData->sOperationModeValue}]">
                [{/foreach}]
            </li>
            <li>
                <label for="cardpanInput">[{oxmultilang ident="FCPO_NUMBER"}]</label>
                <span class="inputIframe" id="cardpan"></span>
            </li>
            [{if $oView->fcpoUseCVC()}]
                <li>
                    <label for="cvcInput">[{oxmultilang ident="FCPO_CARD_SECURITY_CODE"}]</label>
                    <span id="cardcvc2" class="inputIframe"></span>
                </li>
            [{/if}]
            <li>
                <label for="expireInput">[{oxmultilang ident="FCPO_VALID_UNTIL"}]</label>
                <span id="expireInput" class="inputIframe">
                    <span id="cardexpiremonth"></span>
                    <span id="cardexpireyear"></span>
                </span>
            </li>
            <li>
                <label for="firstname">[{oxmultilang ident="FCPO_FIRSTNAME"}]</label>
                <input autocomplete="off" id="firstname" type="text" name="firstname" value="">
            </li>
            <li>
                <label for="lastname">[{oxmultilang ident="FCPO_LASTNAME"}]</label>
                <input autocomplete="off" id="lastname" type="text" name="lastname" value="">
            </li>
            <li>
                <div id="errorOutput"></div>
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
        [{oxid_include_dynamic file=$oViewConf->fcpoGetAbsModuleTemplateFrontendPath('fcpo_payment_creditcard_script.tpl')}]
    </div>
    [{oxscript add="$('#paymentOption_$sPaymentID').find('.dropdown').oxDropDown();"}]
[{/if}]