<dl>
    <dt>
        <input id="payment_[{$sPaymentID}]" type="radio" name="paymentid" value="[{$sPaymentID}]" [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked[{/if}]>
        <label for="payment_[{$sPaymentID}]"><b>[{$paymentmethod->oxpayments__oxdesc->value}] [{if $paymentmethod->fAddPaymentSum != 0}]([{$paymentmethod->fAddPaymentSum}] [{$currency->sign}])[{/if}]</b></label>
    </dt>
    <dd class="[{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]activePayment[{/if}]">
        <ul class="form">
            <input type="hidden" name="fcpo_mode_[{$sPaymentID}]" value="[{$paymentmethod->fcpoGetOperationMode()}]">
            [{assign var="blDisplayCampaignMissing" value=false}]
            [{if $blDisplayCampaignMissing == false}]
                [{if $oView->fcpoKlarnaInfoNeeded()}]
                    <li style="width: 850px;">
                        [{oxmultilang ident="FCPO_KLV_INFO_NEEDED"}]
                        <br>
                    </li>
                [{/if}]
                [{if $sPaymentID == "fcpoklarna"}]
                    [{assign var="blKlv" value=true}]
                [{else}]
                    [{assign var="blKlv" value=false}]
                [{/if}]
                [{if $oView->fcpoKlarnaIsTelephoneNumberNeeded()}]
                    <li>
                        <label>[{oxmultilang ident="FCPO_KLV_TELEPHONENUMBER"}]</label>
                        <input autocomplete="off" type="text" size="20" maxlength="64" [{if $blKlv}]name="dynvalue[fcpo_klv_fon]" value="[{$dynvalue.fcpo_klv_fon}]"[{else}]name="dynvalue[fcpo_kls_fon]" value="[{$dynvalue.fcpo_kls_fon}]"[{/if}]>
                        <div [{if $blKlv}]id="fcpo_klv_fon_invalid"[{else}]id="fcpo_kls_fon_invalid"[{/if}] class="fcpo_check_error">
                            <p class="oxValidateError" style="display: block;">
                                [{oxmultilang ident="FCPO_KLV_TELEPHONENUMBER_INVALID"}]
                            </p>
                        </div>
                    </li>
                [{/if}]
                [{if $oView->fcpoKlarnaIsBirthdayNeeded()}]
                    <li style="width: 850px;">
                        <label>[{oxmultilang ident="FCPO_KLV_BIRTHDAY"}]</label>
                        <input autocomplete="off" type="text" size="3" maxlength="2" style="width:auto;margin-right:10px;" [{if $blKlv}]name="dynvalue[fcpo_klv_birthday][day]" value="[{$dynvalue.fcpo_klv_birthday.day}]"[{else}]name="dynvalue[fcpo_kls_birthday][day]" value="[{$dynvalue.fcpo_kls_birthday.day}]"[{/if}] >
                        <input autocomplete="off" type="text" size="3" maxlength="2" style="width:auto;margin-right:10px;" [{if $blKlv}]name="dynvalue[fcpo_klv_birthday][month]" value="[{$dynvalue.fcpo_klv_birthday.month}]"[{else}]name="dynvalue[fcpo_kls_birthday][month]" value="[{$dynvalue.fcpo_kls_birthday.month}]"[{/if}]>
                        <input autocomplete="off" type="text" size="8" maxlength="4" style="width:auto;margin-right:10px;" [{if $blKlv}]name="dynvalue[fcpo_klv_birthday][year]" value="[{$dynvalue.fcpo_klv_birthday.year}]"[{else}]name="dynvalue[fcpo_kls_birthday][year]" value="[{$dynvalue.fcpo_kls_birthday.year}]"[{/if}]> (DD.MM.YYYY)
                        <div [{if $blKlv}]id="fcpo_klv_birthday_invalid"[{else}]id="fcpo_kls_birthday_invalid"[{/if}] class="fcpo_check_error">
                            <p class="oxValidateError" style="display: block;">
                                [{oxmultilang ident="FCPO_KLV_BIRTHDAY_INVALID"}]
                            </p>
                        </div>
                    </li>
                [{/if}]
                [{if $oView->fcpoKlarnaIsAddressAdditionNeeded()}]
                    <li>
                        <label>[{oxmultilang ident="FCPO_KLV_ADDINFO"}]</label>
                        <input autocomplete="off" type="text" size="20" maxlength="64" [{if $blKlv}]name="dynvalue[fcpo_klv_addinfo]" value="[{$dynvalue.fcpo_klv_addinfo}]"[{else}]name="dynvalue[fcpo_kls_addinfo]" value="[{$dynvalue.fcpo_kls_addinfo}]"[{/if}]>
                        <div [{if $blKlv}]id="fcpo_klv_addinfo_invalid"[{else}]id="fcpo_kls_addinfo_invalid"[{/if}] class="fcpo_check_error">
                            <p class="oxValidateError" style="display: block;">
                                [{oxmultilang ident="FCPO_KLV_ADDINFO_INVALID"}]
                            </p>
                        </div>
                    </li>
                [{/if}]
                [{if $oView->fcpoKlarnaIsDelAddressAdditionNeeded()}]
                    <li>
                        <label>[{oxmultilang ident="FCPO_KLV_ADDINFO_DEL"}]</label>
                        <input autocomplete="off" type="text" size="20" maxlength="64" [{if $blKlv}]name="dynvalue[fcpo_klv_del_addinfo]" value="[{$dynvalue.fcpo_klv_del_addinfo}]"[{else}]name="dynvalue[fcpo_kls_del_addinfo]" value="[{$dynvalue.fcpo_kls_del_addinfo}]"[{/if}]>
                        <div [{if $blKlv}]id="fcpo_klv_del_addinfo_invalid"[{else}]id="fcpo_kls_del_addinfo_invalid"[{/if}] class="fcpo_check_error">
                            <p class="oxValidateError" style="display: block;">
                                [{oxmultilang ident="FCPO_KLV_ADDINFO_INVALID"}]
                            </p>
                        </div>
                    </li>
                [{/if}]
                [{if $oView->fcpoKlarnaIsGenderNeeded()}]
                    <li>
                        <label>[{oxmultilang ident="FCPO_KLV_SAL"}]</label>
                        [{if $blKlv}]
                            [{include file="form/fieldset/salutation.tpl" name="dynvalue[fcpo_klv_sal]" value=$dynvalue.fcpo_klv_sal}]
                        [{else}]
                            [{include file="form/fieldset/salutation.tpl" name="dynvalue[fcpo_kls_sal]" value=$dynvalue.fcpo_kls_sal}]
                        [{/if}]
                    </li>
                [{/if}]
                [{if $oView->fcpoKlarnaIsPersonalIdNeeded()}]
                    <li>
                        <label>[{oxmultilang ident="FCPO_KLV_PERSONALID"}]</label>
                        <input autocomplete="off" type="text" size="20" maxlength="64" [{if $blKlv}]name="dynvalue[fcpo_klv_personalid]" value="[{$dynvalue.fcpo_klv_personalid}]"[{else}]name="dynvalue[fcpo_kls_personalid]" value="[{$dynvalue.fcpo_kls_personalid}]"[{/if}]>
                        <div [{if $blKlv}]id="fcpo_klv_personalid_invalid"[{else}]id="fcpo_kls_personalid_invalid"[{/if}] class="fcpo_check_error">
                            <p class="oxValidateError" style="display: block;">
                                [{oxmultilang ident="FCPO_KLV_PERSONALID_INVALID"}]
                            </p>
                        </div>
                    </li>
                [{/if}]
                <li style="width: 850px;">
                    [{if $oView->fcpoKlarnaInfoNeeded()}]
                        <br>
                    [{/if}]
                    <div style="float:left;width: 32px;">
                        [{if $blKlv}]
                            <input type="hidden"   name="dynvalue[fcpo_klv_confirm]" value="false">
                            <input type="checkbox" name="dynvalue[fcpo_klv_confirm]" value="true" [{if $dynvalue.fcpo_klv_confirm}]checked[{/if}]>
                        [{else}]
                            <input type="hidden"   name="dynvalue[fcpo_kls_confirm]" value="false">
                            <input type="checkbox" name="dynvalue[fcpo_kls_confirm]" value="true" [{if $dynvalue.fcpo_kls_confirm}]checked[{/if}]>
                        [{/if}]
                    </div>
                    <div style="float:left;width: 800px;">
                        [{$oView->fcpoGetConfirmationText()}]
                    </div>
                    <div style="clear:both;"></div>
                    <div [{if $blKlv}]id="fcpo_klv_confirmation_missing"[{else}]id="fcpo_kls_confirmation_missing"[{/if}] class="fcpo_check_error">
                        <p class="oxValidateError" style="display: block;padding-left:32px;">
                            [{oxmultilang ident="FCPO_KLV_CONFIRMATION_MISSING"}]
                        </p>
                    </div>
                </li>
            [{else}]
                <li>[{oxmultilang ident="FCPO_KLS_NO_CAMPAIGN"}]</li>
                <input type="hidden" name="fcpo_klarna_no_campaign" value="true">
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