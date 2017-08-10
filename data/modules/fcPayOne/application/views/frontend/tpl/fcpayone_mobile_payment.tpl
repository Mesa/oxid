<div class="payment-row">
    [{assign var="iPayError" value=$oView->getPaymentError() }]
    [{ if $iPayError == -20}]
        <div class="status error">[{ $oView->getPaymentErrorText() }]</div>
    [{/if}]

    [{oxscript include=$oViewConf->fcpoGetModuleJsPath('fcPayOne.js')}]
    <script type="text/javascript" src="https://secure.pay1.de/client-api/js/ajax.js"></script>
    <style type="text/css">
        .fcpo_check_error, #fcpo_elv_error, #fcpo_elv_error_blocked, #fcpo_cc_error, #fcpo_ou_error {
            display: none;
        }

        .errorbox {
             background-color: red;
             color: white;
        }
    </style>
    
    [{oxscript include="js/widgets/oxinputvalidator.js" priority=10}]
    [{capture name="oxValidate"}]
    if (!((document.all && !document.querySelector) || (document.all && document.querySelector && !document.addEventListener))) {
        $('form.js-oxValidate').oxInputValidator();
    }
    [{/capture}]
    [{oxscript add=$smarty.capture.oxValidate}]
    <form autocomplete="off" action="[{ $oViewConf->getSslSelfLink() }]" class="js-oxValidate payment" id="payment" name="order" method="post" onsubmit="return fcCheckPaymentSelection();">
        <div>
            [{$oViewConf->getHiddenSid()}]
            [{$oViewConf->getNavFormParams()}]
            <input type="hidden" name="cl" value="[{$oViewConf->getActiveClassName()}]" />
            <input type="hidden" name="fnc" value="validatepayment" />
            
            <input type="hidden" name="fcpo_mid" value="[{$oView->getMerchantId()}]">
            <input type="hidden" name="fcpo_portalid" value="[{$oView->getPortalId()}]">
            <input type="hidden" name="fcpo_encoding" value="[{$oView->getEncoding()}]">
            <input type="hidden" name="fcpo_aid" value="[{$oView->getSubAccountId()}]">
            <input type="hidden" name="fcpo_amount" value="[{$oView->getAmount()}]">
            <input type="hidden" name="fcpo_currency" value="[{ $currency->name}]">
            <input type="hidden" name="fcpo_tpllang" value="[{$oView->getTplLang()}]">
            <input type="hidden" name="fcpo_bill_country" value="[{$oView->fcGetBillCountry()}]">
            <input type="hidden" name="dynvalue[fcpo_pseudocardpan]" value="">
            <input type="hidden" name="dynvalue[fcpo_ccmode]" value="">
            <input type="hidden" name="fcpo_checktype" value="[{$oView->getChecktype()}]">
            <input type="hidden" name="fcpo_hashelvWith" value="[{$oView->getHashELVWithChecktype()}]">
            <input type="hidden" name="fcpo_hashelvWithout" value="[{$oView->getHashELVWithoutChecktype()}]">

            <input type="hidden" name="fcpo_integratorid" value="[{$oView->getIntegratorid()}]">
            <input type="hidden" name="fcpo_integratorver" value="[{$oView->getIntegratorver()}]">
            <input type="hidden" name="fcpo_integratorextver" value="[{$oView->getIntegratorextver()}]">
        </div>

        [{if $oView->getPaymentList()}]
            [{block name="mb_select_payment_list"}]
                [{* first loop is to render payment method selection *}]
                <div id="paymentMethods" class="dropdown">
                    [{* only to track selection within DOM *}]
                    <input type="hidden" id="sPaymentSelected" value="" />
                    <div class="dropdown-toggle" data-toggle="dropdown" data-target="#">
                        <a id="dLabelPaymentSelected" role="button" href="#">
                            <span id="paymentSelected">[{oxmultilang ident="FCPO_TYPE_OF_PAYMENT"}]</span>
                            <i class="glyphicon-chevron-down"></i>
                        </a>
                    </div>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabelPaymentSelected">
                        [{foreach key=sPaymentID from=$oView->getPaymentList() item=paymentmethod name=PaymentSelect}]
                            [{block name="mb_select_payment_dropdown"}]
                            [{assign var=sPaymentName value=$paymentmethod->oxpayments__oxdesc->value}]
                            <li class="dropdown-option">
                                <a tabindex="-1" data-selection-id="[{$sPaymentID}]">[{$sPaymentName}]</a>
                            </li>
                            [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]
                                [{oxscript add="$('#sPaymentSelected').val('$sPaymentID');"}]
                            [{/if}]
                            [{/block}]
                        [{/foreach}]
                    </ul>
                </div>
                [{* second loop is to render payment method details *}]
                [{foreach key=sPaymentID from=$oView->getPaymentList() item=paymentmethod name=PaymentSelect}]
                    [{block name="mb_select_payment"}]
                        [{if $sPaymentID == "oxidcashondel"}]
                            [{include file="page/checkout/inc/payment_oxidcashondel.tpl"}]
                        [{elseif $sPaymentID == "oxidcreditcard"}]
                            [{include file="page/checkout/inc/payment_oxidcreditcard.tpl"}]
                        [{elseif $sPaymentID == "oxiddebitnote"}]
                            [{include file="page/checkout/inc/payment_oxiddebitnote.tpl"}]
                        [{else}]
                            [{include file="page/checkout/inc/payment_other.tpl"}]
                        [{/if}]
                    [{/block}]
                [{/foreach}]
            [{/block}]

            [{block name="checkout_payment_nextstep"}]
                <ul class="form">
                    [{if $oxcmp_basket->isBelowMinOrderPrice()}]
                        <li><b>[{oxmultilang ident="FCPO_MIN_ORDER_PRICE"}] [{ $oView->getMinOrderPrice() }] [{ $currency->sign }]</b></li>
                    [{else}]
                        <li><input type="submit" id="paymentNextStepBottom" name="userform" class="btn" value="[{oxmultilang ident="FCPO_CONTINUE_TO_NEXT_STEP"}]" /></li>
                        <li><input type="button" id="paymentBackStepBottom" class="btn previous" value="[{oxmultilang ident="FCPO_PREVIOUS_STEP"}]" onclick="window.open('[{oxgetseourl ident=$oViewConf->getOrderLink()}]', '_self');" /></li>
                    [{/if}]
                </ul>
            [{/block}]

        [{elseif $oView->getEmptyPayment()}]
            [{block name="checkout_payment_nopaymentsfound"}]
                <ul class="form">
                    <h3 id="paymentHeader" class="block-head">[{oxmultilang ident="FCPO_PAYMENT_INFORMATION"}]</h3>
                    [{oxifcontent ident="oxnopaymentmethod" object="oCont"}]
                        [{$oCont->oxcontents__oxcontent->value}]
                    [{/oxifcontent}]
                    <input type="hidden" name="paymentid" value="oxempty" />
                    <li><input type="submit" id="paymentNextStepBottom" name="userform" class="btn" value="[{oxmultilang ident="FCPO_CONTINUE_TO_NEXT_STEP"}]" /></li>
                    <li><input type="button" id="paymentBackStepBottom" class="btn previous" value="[{oxmultilang ident="FCPO_PREVIOUS_STEP"}]" onclick="window.open('[{oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=user"}]', '_self');" /></li>
                </ul>
            [{/block}]
        [{/if}]
    </form>
    <script type="text/javascript">
        if(document.getElementById('fcpoCreditcard') && typeof PayoneRequest == 'function') {
            document.getElementById('fcpoCreditcard').style.display = '';
        }
    </script>
</div>