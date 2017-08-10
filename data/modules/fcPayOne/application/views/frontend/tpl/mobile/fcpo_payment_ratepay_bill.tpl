[{if $oView->fcpoRatePayAllowed('fcporp_bill')}]
    <div id="paymentOption_[{$sPaymentID}]" class="payment-option [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]active-payment[{/if}]">
        <input id="payment_[{$sPaymentID}]" type="radio" name="paymentid" value="[{$sPaymentID}]" [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked="checked"[{/if}] />
        <input type="hidden" name="fcpo_mode_[{$sPaymentID}]" value="[{$paymentmethod->fcpoGetOperationMode()}]">
        <link href="[{$oViewConf->fcpoGetModuleCssPath('lightview.css')}]" rel="stylesheet">
        <script src="[{$oViewConf->fcpoGetModuleJsPath('jquery-1.10.1.min.js')}]"></script>
        <script src="[{$oViewConf->fcpoGetModuleJsPath()}]lightview/lightview.js"></script>
        <ul class="form">
            <input type="hidden" name="dynvalue[fcporp_bill_profileid]" value="[{$oView->fcpoGetRatePayMatchedProfile('fcporp_bill')}]">
            [{if $oView->fcpoRatePayShowUstid()}]
                <li>
                    <input placceholder="[{oxmultilang ident="FCPO_RATEPAY_USTID"}]" type='text' name="dynvalue[fcporp_bill_ustid]" value="[{$oView->fcpoGetUserValue('oxustid')}]">
                </li>
            [{/if}]
            [{if $oView->fcpoRatePayShowBirthdate()}]
                <li>
                    <input placceholder="[{oxmultilang ident="FCPO_PAYOLUTION_YEAR"}]" type="text" size="5" maxlength="4" name="dynvalue[fcporp_bill_birthdate_year]" value="[{$oView->fcpoGetBirthdayField('year')}]">&nbsp;
                    <input placceholder="[{oxmultilang ident="FCPO_PAYOLUTION_MONTH"}]" type="text" size="3" maxlength="2" name="dynvalue[fcporp_bill_birthdate_month]" value="[{$oView->fcpoGetBirthdayField('month')}]">&nbsp;
                    <input placceholder="[{oxmultilang ident="FCPO_PAYOLUTION_DAY"}]" type="text" size="3" maxlength="2" name="dynvalue[fcporp_bill_birthdate_day]" value="[{$oView->fcpoGetBirthdayField('day')}]">
                </li>
            [{/if}]
            [{if $oView->fcpoRatePayShowFon()}]
                <li>
                    <input placceholder="[{oxmultilang ident="FCPO_RATEPAY_FON"}]" type='text' name="dynvalue[fcporp_bill_fon]" value="[{$oView->fcpoGetUserValue('oxfon')}]">
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
    </div>
[{/if}]