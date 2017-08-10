[{if $oView->hasPaymentMethodAvailableSubTypes('sb')}]
    <div id="paymentOption_[{$sPaymentID}]" class="payment-option [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]active-payment[{/if}]">
        <input id="payment_[{$sPaymentID}]" type="radio" name="paymentid" value="[{$sPaymentID}]" [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked="checked"[{/if}] />
        <input type="hidden" name="fcpo_mode_[{$sPaymentID}]" value="[{$paymentmethod->fcpoGetOperationMode()}]">
        <ul class="form">
            <li id="fcpo_ou_error">
                <div class="validation-error" style="display: block;padding: 0;">
                    [{oxmultilang ident="FCPO_ERROR"}]<div id="fcpo_ou_error_content"></div>
                </div>
            </li>
            <li>
                <div class="dropdown">
                    <input type="hidden" id="sFcpoSoTypeSelected" name="dynvalue[fcpo_sotype]" value="[{$oView->getDefaultOnlineUeberweisung()}]" onchange="fcCheckOUType(this);
                            return false;" />
                    <div class="dropdown-toggle" data-toggle="dropdown" data-target="#">
                        <a id="dLabelFcpoSoTypeSelected" role="button" href="#">
                            <span id="fcpoSoTypeSelected">[{oxmultilang ident="FCPO_ONLINE_UEBERWEISUNG_TYPE"}]</span>
                            <i class="glyphicon-chevron-down"></i>
                        </a>
                    </div>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabelFcpoSoTypeSelected">
                        [{foreach var=$aFcPoOnlinePaymentMetaData item="oPaymentMetaData"}]
                            <li class="dropdown-option"><a tabindex="-1" data-selection-id="[{$oPaymentMetaData->sShortcut}]">[{$oPaymentMetaData->sCaption}]</a></li>
                            [{/foreach}]
                    </ul>
                    [{if !empty($dynvalue.fcpo_sotype)}]
                        [{oxscript add="$('#sFcpoSoTypeSelected').val('"|cat:$dynvalue.fcpo_sotype|cat:"');"}]
                    [{/if}]
                </div>
            </li>
            <li id="fcpo_ou_iban">
                <input type="text" size="20" maxlength="64" name="dynvalue[fcpo_ou_iban]" autocomplete="off" value="[{$dynvalue.fcpo_ou_iban}]" placeholder="[{oxmultilang ident="FCPO_BANK_IBAN"}]" />
                <div id="fcpo_ou_iban_invalid" class="fcpo_check_error">
                    <p class="validation-error" style="display: block;">
                        [{oxmultilang ident="FCPO_IBAN_INVALID"}]
                    </p>
                </div>
            </li>
            <li id="fcpo_ou_bic">
                <input type="text" size="20" maxlength="64" name="dynvalue[fcpo_ou_bic]" autocomplete="off" value="[{$dynvalue.fcpo_ou_bic}]" placeholder="[{oxmultilang ident="FCPO_BANK_BIC"}]" />
                <div id="fcpo_ou_bic_invalid" class="fcpo_check_error">
                    <p class="validation-error" style="display: block;">
                        [{oxmultilang ident="FCPO_BIC_INVALID"}]
                    </p>
                </div>
            </li>
            <li id="fcpo_ou_blz">
                <input type="text" size="20" maxlength="64" name="dynvalue[fcpo_ou_blz]" autocomplete="off" value="[{$dynvalue.fcpo_ou_blz}]" placeholder="[{oxmultilang ident="FCPO_BANK_CODE"}]" />
                <div id="fcpo_ou_blz_invalid" class="fcpo_check_error">
                    <p class="validation-error" style="display: block;">
                        [{oxmultilang ident="FCPO_BLZ_INVALID"}]
                    </p>
                </div>
            </li>
            <li id="fcpo_ou_ktonr">
                <input type="text" size="20" maxlength="64" name="dynvalue[fcpo_ou_ktonr]" autocomplete="off" value="[{$dynvalue.fcpo_ou_ktonr}]" placeholder="[{oxmultilang ident="FCPO_BANK_ACCOUNT_NUMBER"}]" />
                <div id="fcpo_ou_ktonr_invalid" class="fcpo_check_error">
                    <p class="validation-error" style="display: block;">
                        [{oxmultilang ident="FCPO_KTONR_INVALID"}]
                    </p>
                </div>
            </li>
            <li id="fcpo_ou_eps" style="display: none;">
                <div class="dropdown">
                    <input type="hidden" id="sFcpoSoBanktypeEpsSelected" name="dynvalue[fcpo_so_bankgrouptype_eps]" value="ARZ_OVB" />
                    <div class="dropdown-toggle" data-toggle="dropdown" data-target="#">
                        <a id="dLabelFcpoSoBanktypeEpsSelected" role="button" href="#">
                            <span id="fcpoSoBanktypeEpsSelected">[{oxmultilang ident="FCPO_BANKGROUPTYPE"}]</span>
                            <i class="glyphicon-chevron-down"></i>
                        </a>
                    </div>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabelFcpoSoBanktypeEpsSelected">
                        <li class="dropdown-option"><a tabindex="-1" data-selection-id="ARZ_OVB">Volksbanken</a></li>
                        <li class="dropdown-option"><a tabindex="-1" data-selection-id="ARZ_BAF">Bank f&uuml;r &Auml;rzte und Freie Berufe</a></li>
                        <li class="dropdown-option"><a tabindex="-1" data-selection-id="ARZ_NLH">Nieder&ouml;sterreichische Landes-Hypo</a></li>
                        <li class="dropdown-option"><a tabindex="-1" data-selection-id="ARZ_VLH">Vorarlberger Landes-Hypo</a></li>
                        <li class="dropdown-option"><a tabindex="-1" data-selection-id="ARZ_BCS">Bankhaus Carl Sp&auml;ngler & Co. AG</a></li>
                        <li class="dropdown-option"><a tabindex="-1" data-selection-id="ARZ_HTB">Hypo Tirol</a></li>
                        <li class="dropdown-option"><a tabindex="-1" data-selection-id="ARZ_HAA">Hypo Alpe Adria</a></li>
                        <li class="dropdown-option"><a tabindex="-1" data-selection-id="ARZ_IKB">Investkreditbank</a></li>
                        <li class="dropdown-option"><a tabindex="-1" data-selection-id="ARZ_OAB">&Ouml;sterreichische Apothekerbank</a></li>
                        <li class="dropdown-option"><a tabindex="-1" data-selection-id="ARZ_IMB">Immobank</a></li>
                        <li class="dropdown-option"><a tabindex="-1" data-selection-id="ARZ_GRB">G&auml;rtnerbank</a></li>
                        <li class="dropdown-option"><a tabindex="-1" data-selection-id="ARZ_HIB">HYPO Investment</a></li>
                        <li class="dropdown-option"><a tabindex="-1" data-selection-id="BA_AUS">Bank Austria</a></li>
                        <li class="dropdown-option"><a tabindex="-1" data-selection-id="BAWAG_BWG">BAWAG</a></li>
                        <li class="dropdown-option"><a tabindex="-1" data-selection-id="BAWAG_PSK">PSK Bank</a></li>
                        <li class="dropdown-option"><a tabindex="-1" data-selection-id="BAWAG_ESY">easybank</a></li>
                        <li class="dropdown-option"><a tabindex="-1" data-selection-id="BAWAG_SPD">Sparda Bank</a></li>
                        <li class="dropdown-option"><a tabindex="-1" data-selection-id="SPARDAT_EBS">Erste Bank</a></li>
                        <li class="dropdown-option"><a tabindex="-1" data-selection-id="SPARDAT_BBL">Bank Burgenland</a></li>
                        <li class="dropdown-option"><a tabindex="-1" data-selection-id="RAC_RAC">Raiffeisen</a></li>
                        <li class="dropdown-option"><a tabindex="-1" data-selection-id="HRAC_OOS">Hypo Ober&ouml;sterreich</a></li>
                        <li class="dropdown-option"><a tabindex="-1" data-selection-id="HRAC_SLB">Hypo Salzburg</a></li>
                        <li class="dropdown-option"><a tabindex="-1" data-selection-id="HRAC_STM">Hypo Steiermark</a></li>
                    </ul>
                    [{if !empty($dynvalue.fcpo_so_bankgrouptype_eps)}]
                        [{oxscript add="$('#sFcpoSoBanktypeEpsSelected').val('"|cat:$dynvalue.fcpo_so_bankgrouptype_eps|cat:"');"}]
                    [{/if}]
                </div>
            </li>
            <li id="fcpo_ou_idl" style="display: none;">
                <div class="dropdown">
                    <input type="hidden" id="sFcpoSoBanktypeIdlSelected" name="dynvalue[fcpo_so_bankgrouptype_idl]" value="ABN_AMRO_BANK" />
                    <div class="dropdown-toggle" data-toggle="dropdown" data-target="#">
                        <a id="dLabelFcpoSoBanktypeIdlSelected" role="button" href="#">
                            <span id="fcpoSoBanktypeIdlSelected">[{oxmultilang ident="FCPO_BANKGROUPTYPE"}]</span>
                            <i class="glyphicon-chevron-down"></i>
                        </a>
                    </div>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabelFcpoSoBanktypeIdlSelected">
                        <li class="dropdown-option"><a tabindex="-1" data-selection-id="ABN_AMRO_BANK">ABN Amro</a></li>
                        <li class="dropdown-option"><a tabindex="-1" data-selection-id="BUNQ_BANK">Bunq</a></li>
                        <li class="dropdown-option"><a tabindex="-1" data-selection-id="ING_BANK">ING Bank</a></li>
                        <li class="dropdown-option"><a tabindex="-1" data-selection-id="RABOBANK">Rabobank</a></li>
                        <li class="dropdown-option"><a tabindex="-1" data-selection-id="SNS_BANK">SNS Bank</a></li>
                        <li class="dropdown-option"><a tabindex="-1" data-selection-id="ASN_BANK">ASN Bank</a></li>
                        <li class="dropdown-option"><a tabindex="-1" data-selection-id="SNS_REGIO_BANK">Regio Bank</a></li>
                        <li class="dropdown-option"><a tabindex="-1" data-selection-id="TRIODOS_BANK">Triodos Bank</a></li>
                        <li class="dropdown-option"><a tabindex="-1" data-selection-id="KNAB_BANK">Knab</a></li>
                        <li class="dropdown-option"><a tabindex="-1" data-selection-id="VAN_LANSCHOT_BANKIERS">van Lanschot</a></li>
                    </ul>
                    [{if !empty($dynvalue.fcpo_so_bankgrouptype_idl)}]
                        [{oxscript add="$('#sFcpoSoBanktypeIdlSelected').val('"|cat:$dynvalue.fcpo_so_bankgrouptype_idl|cat:"');"}]
                    [{/if}]
                </div>
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
    </div>
    [{oxscript add="$('#paymentOption_$sPaymentID').find('.dropdown').oxDropDown();"}]
[{/if}]