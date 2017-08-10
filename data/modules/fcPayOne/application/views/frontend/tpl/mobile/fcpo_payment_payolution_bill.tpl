<div id="paymentOption_[{$sPaymentID}]" class="payment-option [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]active-payment[{/if}]">
    <input id="payment_[{$sPaymentID}]" type="radio" name="paymentid" value="[{$sPaymentID}]" [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked="checked"[{/if}] />
    <input type="hidden" name="fcpo_mode_[{$sPaymentID}]" value="[{$paymentmethod->fcpoGetOperationMode()}]">
    <link href="[{$oViewConf->fcpoGetModuleCssPath('lightview.css')}]" rel="stylesheet">
    <script src="[{$oViewConf->fcpoGetModuleJsPath('jquery-1.10.1.min.js')}]"></script>
    <script src="[{$oViewConf->fcpoGetModuleJsPath()}]lightview/lightview.js"></script>
    <ul class="form">
        [{if $oView->fcpoShowB2B()}]
            <li>
                <input type="text" size="20" maxlength="64" name="dynvalue[fcpo_payolution_bill_ustid]" autocomplete="off" value="[{$oView->fcpoGetUserValue('oxustid')}]" placeholder="[{oxmultilang ident="FCPO_PAYOLUTION_USTID"}]" />
            </li>
        [{elseif $oView->fcpoShowB2C()}]
            <li>
                <input placceholder="[{oxmultilang ident="FCPO_PAYOLUTION_YEAR"}]" type="text" size="5" maxlength="4" name="dynvalue[fcpo_payolution_bill_birthdate_year]" value="[{$oView->fcpoGetBirthdayField('year')}]">&nbsp;
                <input placceholder="[{oxmultilang ident="FCPO_PAYOLUTION_MONTH"}]" type="text" size="3" maxlength="2" name="dynvalue[fcpo_payolution_bill_birthdate_month]" value="[{$oView->fcpoGetBirthdayField('month')}]">&nbsp;
                <input placceholder="[{oxmultilang ident="FCPO_PAYOLUTION_DAY"}]" type="text" size="3" maxlength="2" name="dynvalue[fcpo_payolution_bill_birthdate_day]" value="[{$oView->fcpoGetBirthdayField('day')}]">
            </li>
        [{/if}]
        <li>
            <input name="dynvalue[fcpo_payolution_bill_agreed]" value="agreed" type="checkbox">&nbsp;[{oxmultilang ident="FCPO_PAYOLUTION_AGREEMENT_PART_1"}] <a href='[{$oView->fcpoGetPayolutionAgreementLink()}]' class="lightview fcpoPayolutionAgreeRed" data-lightview-type="iframe" data-lightview-options="width: 800, height: 600, viewport: 'scale',background: { color: '#fff', opacity: 1 },skin: 'light'">[{oxmultilang ident="FCPO_PAYOLUTION_AGREE"}]</a> [{oxmultilang ident="FCPO_PAYOLUTION_AGREEMENT_PART_2"}]
        </li>
    </ul>
</div>