<!-- FCPAYONE BEGIN -->
[{if $edit->oxpayments__fcpoispayone->value == 1}]
    <tr>
        <td class="edittext" colspan="2">
            <img src="[{$oViewConf->fcpoGetAdminModuleImgUrl()}]logoclaim.gif" alt="PAYONE"><br><br>
            [{oxmultilang ident="FC_IS_PAYONE"}]
            [{if $edit->oxpayments__oxid->value == 'fcpobarzahlen'}]
                    <input type="hidden" name="editval[oxpayments__fcpoauthmode]" value="preauthorization">
            [{/if}]
        </td>
    </tr>
    <tr>
    [{if $edit->fcpoAuthorizeAllowed()}]
        <td class="edittext" width="70">
            [{oxmultilang ident="FCPO_AUTHORIZATION_METHOD"}]
        </td>
        <td class="edittext">
            <input type="radio" name="editval[oxpayments__fcpoauthmode]" value="preauthorization" [{if $edit->oxpayments__fcpoauthmode->value == 'preauthorization'}]checked[{/if}]> [{oxmultilang ident="FCPO_PREAUTHORIZATION"}] [{oxinputhelp ident="FCPO_PREAUTHORIZATION_HELP"}]<br>
            <input type="radio" name="editval[oxpayments__fcpoauthmode]" value="authorization" [{if $edit->oxpayments__fcpoauthmode->value == 'authorization'}]checked[{/if}]> [{oxmultilang ident="FCPO_AUTHORIZATION"}] [{oxinputhelp ident="FCPO_AUTHORIZATION_HELP"}]
        </td>
    [{/if}]
    </tr>
    <tr>
        <td class="edittext" width="70">
            [{oxmultilang ident="FCPO_OPERATION_MODE"}]
        </td>
        <td class="edittext">
            [{if $edit->getId() == 'fcpocreditcard' || $edit->getId() == 'fcpoonlineueberweisung'}]
                [{oxmultilang ident="FCPO_INFOTEXT_SET_OPERATIONMODE"}]
            [{else}]
                <table>
                    <tr>
                        <td>
                            <input type="radio" name="editval[oxpayments__fcpolivemode]" value="1" [{if $edit->oxpayments__fcpolivemode->value == '1'}]checked[{/if}]> <strong>[{oxmultilang ident="FCPO_LIVE_MODE"}]</strong><br>
                            <input type="radio" name="editval[oxpayments__fcpolivemode]" value="0" [{if $edit->oxpayments__fcpolivemode->value == '0'}]checked[{/if}]> [{oxmultilang ident="FCPO_TEST_MODE"}]<br>
                        </td>
                        <td>
                            [{oxinputhelp ident="FCPO_HELP_OPERATIONMODE"}]
                        </td>
                    </tr>
                </table>
            [{/if}]
        </td>
    </tr>
[{else}]
    <tr>
        <td colspan="2">
            <input type="hidden" name="editval[oxpayments__fcpoauthmode]" value="">
        </td>
    </tr>
[{/if}]
<!-- FCPAYONE END -->
[{$smarty.block.parent}]