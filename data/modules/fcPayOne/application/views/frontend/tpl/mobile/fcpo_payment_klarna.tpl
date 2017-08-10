<div id="paymentOption_[{$sPaymentID}]" class="payment-option [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]active-payment[{/if}]">
    <input id="payment_[{$sPaymentID}]" type="radio" name="paymentid" value="[{$sPaymentID}]" [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked="checked"[{/if}] />
    <input type="hidden" name="fcpo_mode_[{$sPaymentID}]" value="[{$paymentmethod->fcpoGetOperationMode()}]">
    <ul class="form">
        [{assign var="blDisplayCampaignMissing" value=false}]
        [{if $blDisplayCampaignMissing == false}]
            [{if $oView->fcpoKlarnaInfoNeeded()}]
                <li>
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
                    <input type="text" size="20" maxlength="64" [{if $blKlv}]name="dynvalue[fcpo_klv_fon]" value="[{$dynvalue.fcpo_klv_fon}]"[{else}]name="dynvalue[fcpo_kls_fon]" value="[{$dynvalue.fcpo_kls_fon}]"[{/if}] autocomplete="off" placeholder="[{oxmultilang ident="FCPO_KLV_TELEPHONENUMBER"}]" />
                    <div [{if $blKlv}]id="fcpo_klv_fon_invalid"[{else}]id="fcpo_kls_fon_invalid"[{/if}] class="fcpo_check_error">
                        <p class="validation-error" style="display: block;">
                            [{oxmultilang ident="FCPO_KLV_TELEPHONENUMBER_INVALID"}]
                        </p>
                    </div>
                </li>
            [{/if}]
            [{if $oView->fcpoKlarnaIsBirthdayNeeded()}]
                [{oxscript include="js/libs/modernizr.custom.min.js" priority=10}]
                [{oxscript include="js/widgets/oxdatepicker.js" priority=10}]
                [{oxscript add="$('#datePicker').oxDatePicker();"}]
                [{if $blKlv}]
                    [{assign var="iBirthdayDay" value=$dynvalue.fcpo_klv_birthday.day}]
                    [{assign var="iBirthdayMonth" value=$dynvalue.fcpo_klv_birthday.month}]
                    [{assign var="iBirthdayYear" value=$dynvalue.fcpo_klv_birthday.year}]
                [{else}]
                    [{assign var="iBirthdayDay" value=$dynvalue.fcpo_kls_birthday.day}]
                    [{assign var="iBirthdayMonth" value=$dynvalue.fcpo_kls_birthday.month}]
                    [{assign var="iBirthdayYear" value=$dynvalue.fcpo_kls_birthday.year}]
                [{/if}]

                <li class="oxDate">
                    <label>[{oxmultilang ident="FCPO_KLV_BIRTHDAY"}]</label>
                    <div id="datePicker">
                        <ul class="nav nav-pills nav-justified datepicker-container">
                            <li id="month">
                                <button class="btn" type="button">+</button>
                                <input [{if $blKlv}]name="dynvalue[fcpo_klv_birthday][month]"[{else}]name="dynvalue[fcpo_kls_birthday][month]"[{/if}] type="hidden" value="[{if $iBirthdayMonth > 0}][{$iBirthdayMonth}][{/if}]" />
                                <input placeholder="[{oxmultilang ident="MONTH"}]" type="text" readonly/>
                                <button class="btn" type="button">-</button>
                            </li>
                            <li id="day">
                                <button class="btn" type="button">+</button>
                                <input data-fieldsize="xsmall" id="oxDay" maxlength="2" [{if $blKlv}]name="dynvalue[fcpo_klv_birthday][day]"[{else}]name="dynvalue[fcpo_kls_birthday][day]"[{/if}] placeholder="[{oxmultilang ident="DAY"}]" type="number" value="[{if $iBirthdayDay > 0}][{$iBirthdayDay}][{/if}]" />
                                <button class="btn" type="button">-</button>
                            </li>
                            <li id="year">
                                <button class="btn" type="button">+</button>
                                <input data-fieldsize="small" id="oxYear" maxlength="4"  [{if $blKlv}]name="dynvalue[fcpo_klv_birthday][year]"[{else}]name="dynvalue[fcpo_kls_birthday][year]"[{/if}] placeholder="[{oxmultilang ident="YEAR"}]" type="number" value="[{if $iBirthdayYear}][{$iBirthdayYear}][{/if}]" />
                                <button class="btn" type="button">-</button>
                            </li>
                            <li class="months">
                                <select id="months">
                                    [{section name="month" start=1 loop=13}]
                                        <option value="[{$smarty.section.month.index}]" [{if $iBirthdayMonth == $smarty.section.month.index}] selected="selected" [{/if}]>[{oxmultilang ident="MONTH_NAME_"|cat:$smarty.section.month.index}]</option>
                                    [{/section}]
                                </select>
                            </li>
                        </ul>
                        <div [{if $blKlv}]id="fcpo_klv_birthday_invalid"[{else}]id="fcpo_kls_birthday_invalid"[{/if}] class="fcpo_check_error">
                            <p class="validation-error" style="display: block;">
                                [{oxmultilang ident="FCPO_KLV_BIRTHDAY_INVALID"}]
                            </p>
                        </div>
                    </div>
                    <input id="modernDate" type="date" value="[{if $iBirthdayDay > 0}][{$iBirthdayYear}]-[{if $iBirthdayMonth < 10}]0[{/if}][{$iBirthdayMonth}]-[{$iBirthdayDay}][{/if}]"/>
                </li>
            [{/if}]
            [{if $oView->fcpoKlarnaIsAddressAdditionNeeded()}]
                <li>
                    <input type="text" size="20" maxlength="64" [{if $blKlv}]name="dynvalue[fcpo_klv_addinfo]" value="[{$dynvalue.fcpo_klv_addinfo}]"[{else}]name="dynvalue[fcpo_kls_addinfo]" value="[{$dynvalue.fcpo_kls_addinfo}]"[{/if}] autocomplete="off" placeholder="[{oxmultilang ident="FCPO_KLV_ADDINFO"}]" />
                    <div [{if $blKlv}]id="fcpo_klv_addinfo_invalid"[{else}]id="fcpo_kls_addinfo_invalid"[{/if}] class="fcpo_check_error">
                        <p class="validation-error" style="display: block;">
                            [{oxmultilang ident="FCPO_KLV_ADDINFO_INVALID"}]
                        </p>
                    </div>
                </li>
            [{/if}]
            [{if $oView->fcpoKlarnaIsDelAddressAdditionNeeded()}]
                <li>
                    <input type="text" size="20" maxlength="64" [{if $blKlv}]name="dynvalue[fcpo_klv_del_addinfo]" value="[{$dynvalue.fcpo_klv_del_addinfo}]"[{else}]name="dynvalue[fcpo_kls_del_addinfo]" value="[{$dynvalue.fcpo_kls_del_addinfo}]"[{/if}] autocomplete="off" placeholder="[{oxmultilang ident="FCPO_KLV_ADDINFO_DEL"}]" />
                    <div [{if $blKlv}]id="fcpo_klv_del_addinfo_invalid"[{else}]id="fcpo_kls_del_addinfo_invalid"[{/if}] class="fcpo_check_error">
                        <p class="validation-error" style="display: block;">
                            [{oxmultilang ident="FCPO_KLV_ADDINFO_INVALID"}]
                        </p>
                    </div>
                </li>
            [{/if}]
            [{if $oView->fcpoKlarnaIsGenderNeeded()}]
                <li>
                    [{if $blKlv}]
                        [{include file="form/fieldset/salutation.tpl" name="dynvalue[fcpo_klv_sal]" value=$dynvalue.fcpo_klv_sal}]
                    [{else}]
                        [{include file="form/fieldset/salutation.tpl" name="dynvalue[fcpo_kls_sal]" value=$dynvalue.fcpo_kls_sal}]
                    [{/if}]
                </li>
            [{/if}]
            [{if $oView->fcpoKlarnaIsPersonalIdNeeded()}]
                <li>
                    <input type="text" size="20" maxlength="64" [{if $blKlv}]name="dynvalue[fcpo_klv_personalid]" value="[{$dynvalue.fcpo_klv_personalid}]"[{else}]name="dynvalue[fcpo_kls_personalid]" value="[{$dynvalue.fcpo_kls_personalid}]"[{/if}] autocomplete="off" placeholder="[{oxmultilang ident="FCPO_KLV_PERSONALID"}]" />
                    <div [{if $blKlv}]id="fcpo_klv_personalid_invalid"[{else}]id="fcpo_kls_personalid_invalid"[{/if}] class="fcpo_check_error">
                        <p class="validation-error" style="display: block;">
                            [{oxmultilang ident="FCPO_KLV_PERSONALID_INVALID"}]
                        </p>
                    </div>
                </li>
            [{/if}]
            <li>
                <div style="float:left;width:5%;">
                    [{if $blKlv}]
                        <input type="hidden"   name="dynvalue[fcpo_klv_confirm]" value="false">
                        <input type="checkbox" name="dynvalue[fcpo_klv_confirm]" value="true" [{if $dynvalue.fcpo_klv_confirm}]checked[{/if}]>
                    [{else}]
                        <input type="hidden"   name="dynvalue[fcpo_kls_confirm]" value="false">
                        <input type="checkbox" name="dynvalue[fcpo_kls_confirm]" value="true" [{if $dynvalue.fcpo_kls_confirm}]checked[{/if}]>
                    [{/if}]
                </div>
                <div style="float:left;width:95%;">
                    [{$oView->fcpoGetConfirmationText()}]
                </div>
                <div style="clear:both;"></div>
                <div [{if $blKlv}]id="fcpo_klv_confirmation_missing"[{else}]id="fcpo_kls_confirmation_missing"[{/if}] class="fcpo_check_error">
                    <p class="validation-error" style="display: block;padding-left:5%;">
                        [{oxmultilang ident="FCPO_KLV_CONFIRMATION_MISSING"}]
                    </p>
                </div>
            </li>
        [{else}]
            <li>[{oxmultilang ident="FCPO_KLS_NO_CAMPAIGN"}]</li>
            <input type="hidden" name="fcpo_klarna_no_campaign" value="true">
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