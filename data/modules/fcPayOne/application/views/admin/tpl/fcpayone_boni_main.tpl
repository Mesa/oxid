[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
<!--
function loadLang(obj) {
    var langvar = document.getElementById("agblang");
    if (langvar != null ) {
        langvar.value = obj.value;
    }
    document.myedit.submit();
}
//-->
</script>

[{ if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form autocomplete="off" name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="fcpayone_boni_main">
</form>

<form autocomplete="off" name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="cl" value="fcpayone_boni_main">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="oxid" value="[{ $oxid }]">

    <strong>[{ oxmultilang ident="FCPO_BONICHECK_CONFIG_INFOTEXT" }]</strong><br><br>
    [{ oxmultilang ident="FCPO_BONICHECK_CONFIG_INFOTEXT_SMALL" }]<br><br>

    <table border="0" width="98%">

        <tr>
            <td class="edittext" width="150">[{ oxmultilang ident="FCPO_BONI_OPERATION_MODE" }]</td>
            <td class="edittext" width="250"><input type="radio" name="confstrs[sFCPOBoniOpMode]" value="live" [{if $confstrs.sFCPOBoniOpMode == 'live'}]checked[{/if}]> <strong>[{ oxmultilang ident="FCPO_LIVE_MODE" }]</strong></td>
            <td class="edittext">[{ oxinputhelp ident="FCPO_HELP_BONI_OPERATIONMODE" }]</td>
        </tr>
        <tr>
            <td class="edittext"></td>
            <td class="edittext"><input type="radio" name="confstrs[sFCPOBoniOpMode]" value="test" [{if $confstrs.sFCPOBoniOpMode == 'test'}]checked[{/if}]> [{ oxmultilang ident="FCPO_TEST_MODE" }]<br></td>
            <td class="edittext"></td>
        </tr>
        <tr><td colspan="3">&nbsp;</td></tr>

        <tr>
            <td class="edittext">[{ oxmultilang ident="FCPO_CONSUMERSCORETYPE"}]</td>
            <td class="edittext"><input type="radio" name="confstrs[sFCPOBonicheck]" value="-1" [{if $confstrs.sFCPOBonicheck == '-1'}]checked[{/if}]> [{ oxmultilang ident="FCPO_NO_BONICHECK" }]</td>
            <td class="edittext">[{ oxinputhelp ident="FCPO_HELP_NO_BONICHECK" }]</td>
        </tr>
        <tr>
            <td class="edittext"></td>
            <td class="edittext"><input type="radio" name="confstrs[sFCPOBonicheck]" value="IH" [{if $confstrs.sFCPOBonicheck == 'IH'}]checked[{/if}]> [{ oxmultilang ident="FCPO_HARD_BONICHECK" }]</td>
            <td class="edittext">[{ oxinputhelp ident="FCPO_HELP_HARD_BONICHECK" }]</td>
        </tr>
        <tr>
            <td class="edittext"></td>
            <td class="edittext"><input type="radio" name="confstrs[sFCPOBonicheck]" value="IA" [{if $confstrs.sFCPOBonicheck == 'IA'}]checked[{/if}]> [{ oxmultilang ident="FCPO_ALL_BONICHECK" }]</td>
            <td class="edittext">[{ oxinputhelp ident="FCPO_HELP_ALL_BONICHECK" }]<br></td>
        </tr>
        <tr>
            <td class="edittext"></td>
            <td class="edittext"><input type="radio" name="confstrs[sFCPOBonicheck]" value="IB" [{if $confstrs.sFCPOBonicheck == 'IB'}]checked[{/if}]> [{ oxmultilang ident="FCPO_ALL_SCORE_BONICHECK" }]</td>
            <td class="edittext">[{ oxinputhelp ident="FCPO_HELP_ALL_SCORE_BONICHECK" }]<br></td>
        </tr>
        <tr><td colspan="3">&nbsp;</td></tr>        
        <tr valign="top">
            <td class="edittext" >
                [{ oxmultilang ident="FCPO_CONSUMERSCORE_MOMENT"}]
            </td>
            <td class="edittext">
                <input type="radio" name="confstrs[sFCPOBonicheckMoment]" value="before" [{if !isset( $confstrs.sFCPOBonicheckMoment) || $confstrs.sFCPOBonicheckMoment == 'before'}]checked[{/if}]> [{ oxmultilang ident="FCPO_CONSUMERSCORE_BEFORE" }]<br>
                <input type="radio" name="confstrs[sFCPOBonicheckMoment]" value="after" [{if $confstrs.sFCPOBonicheckMoment == 'after'}]checked[{/if}]> [{ oxmultilang ident="FCPO_CONSUMERSCORE_AFTER" }]
            </td>
            <td class="edittext">[{ oxinputhelp ident="FCPO_HELP_CONSUMERSCORE_MOMENT" }]</td>
        </tr>        
        <tr><td colspan="3">&nbsp;</td></tr>
        <tr>
            <td class="edittext">[{ oxmultilang ident="FCPO_DURABILITY_BONICHECK"}]</td>
            <td class="edittext"><input type=text name="confstrs[sFCPODurabilityBonicheck]" value="[{$confstrs.sFCPODurabilityBonicheck}]"></td>
            <td class="edittext">[{ oxinputhelp ident="FCPO_HELP_DURABILITY_BONICHECK" }]<br></td>
        </tr>
        <tr>
            <td class="edittext">[{ oxmultilang ident="FCPO_STARTLIMIT_BONICHECK"}] ([{ $oActCur->sign }])</td>
            <td class="edittext"><input type=text name="confstrs[sFCPOStartlimitBonicheck]" value="[{$confstrs.sFCPOStartlimitBonicheck}]"></td>
            <td class="edittext">[{ oxinputhelp ident="FCPO_HELP_STARTLIMIT_BONICHECK" }]<br></td>
        </tr>
        <tr>
            <td class="edittext">[{ oxmultilang ident="FCPO_DEFAULT_BONI"}]</td>
            <td class="edittext"><input type=text name="confstrs[sFCPODefaultBoni]" value="[{$confstrs.sFCPODefaultBoni}]"></td>
            <td class="edittext">[{ oxinputhelp ident="FCPO_HELP_DEFAULT_BONI" }]<br></td>
        </tr>
        <tr><td colspan="3">&nbsp;</td></tr>

        <tr>
            <td class="edittext">[{ oxmultilang ident="FCPO_ADDRESSCHECKTYPE"}]</td>
            <td class="edittext"><input type="radio" name="confstrs[sFCPOAddresscheck]" value="NO" [{if $confstrs.sFCPOAddresscheck == 'NO'}]checked[{/if}]> [{ oxmultilang ident="FCPO_NO_ADDRESSCHECK" }]</td>
            <td class="edittext">[{ oxinputhelp ident="FCPO_HELP_NO_ADDRESSCHECK" }]<br></td>
        </tr>
        <tr>
            <td class="edittext"></td>
            <td class="edittext"><input type="radio" name="confstrs[sFCPOAddresscheck]" value="BA" [{if $confstrs.sFCPOAddresscheck == 'BA'}]checked[{/if}]> [{ oxmultilang ident="FCPO_BASIC_ADDRESSCHECK" }]</td>
            <td class="edittext">[{ oxinputhelp ident="FCPO_HELP_BASIC_ADDRESSCHECK" }]</td>
        </tr>
        <tr>
            <td class="edittext"></td>
            <td class="edittext"><input type="radio" name="confstrs[sFCPOAddresscheck]" value="PE" [{if $confstrs.sFCPOAddresscheck == 'PE'}]checked[{/if}]> [{ oxmultilang ident="FCPO_PERSON_ADDRESSCHECK" }]</td>
            <td class="edittext">[{ oxinputhelp ident="FCPO_HELP_PERSON_ADDRESSCHECK" }]<br></td>
        </tr>
        <tr>
            <td class="edittext"></td>
            <td class="edittext">
                <input type=hidden name="confbools[blFCPOCorrectAddress]" value="false">
                <input type=checkbox name="confbools[blFCPOCorrectAddress]" value="true"  [{if ($confbools.blFCPOCorrectAddress)}]checked[{/if}]> [{ oxmultilang ident="FCPO_CORRECT_ADDRESS" }]
            </td>
            <td class="edittext">[{ oxinputhelp ident="FCPO_HELP_CORRECT_ADDRESS" }]<br></td>
        </tr>
        <tr>
            <td class="edittext"></td>
            <td class="edittext">
                <input type=hidden name="confbools[blFCPOCheckDelAddress]" value="false">
                <input type=checkbox name="confbools[blFCPOCheckDelAddress]" value="true"  [{if ($confbools.blFCPOCheckDelAddress)}]checked[{/if}]> [{ oxmultilang ident="FCPO_CHECK_DEL_ADDRESS" }]
            </td>
            <td class="edittext">[{ oxinputhelp ident="FCPO_HELP_CHECK_DEL_ADDRESS" }]<br></td>
        </tr>
        <tr>
            <td class="edittext"></td>
            <td class="edittext" colspan="2">
                <input type=hidden name="confbools[blFCPOAddCheckPPF]" value="false">
                <input type=checkbox name="confbools[blFCPOAddCheckPPF]" value="true" [{if ($confbools.blFCPOAddCheckPPF)}]checked[{/if}]> [{ oxmultilang ident="FCPO_ADDRESSCHECK_PPF"}]
            </td>
        </tr>
        <tr><td colspan="3">&nbsp;</td></tr>

        <tr valign="top">
            <td class="edittext" >
                [{ oxmultilang ident="FCPO_BANKACCOUNTCHECK"}]
            </td>
            <td class="edittext">
                <input type="radio" name="confstrs[sFCPOPOSCheck]" value="-1" [{if $confstrs.sFCPOPOSCheck == '-1'}]checked[{/if}]> [{ oxmultilang ident="FCPO_DEACTIVATED" }]<br>
                <input type="radio" name="confstrs[sFCPOPOSCheck]" value="0" [{if $confstrs.sFCPOPOSCheck == '0'}]checked[{/if}]> [{ oxmultilang ident="FCPO_ACTIVATED" }]<br>
                <input type="radio" name="confstrs[sFCPOPOSCheck]" value="1" [{if $confstrs.sFCPOPOSCheck == '1'}]checked[{/if}]> [{ oxmultilang ident="FCPO_ACTIVATEDWITHPOS" }]
            </td>
            <td class="edittext">[{ oxinputhelp ident="FCPO_HELP_POSCHECK" }]</td>
        </tr>
        
        <tr><td colspan="3">&nbsp;</td></tr>
        <tr valign="top">
            <td class="edittext" >
                [{ oxmultilang ident="FCPO_SAVEBANKDATA"}]
            </td>
            <td class="edittext">
                <input type="radio" name="confstrs[sFCPOSaveBankdata]" value="0" [{if !isset( $confstrs.sFCPOSaveBankdata) || $confstrs.sFCPOSaveBankdata == '0'}]checked[{/if}]> [{ oxmultilang ident="FCPO_DEACTIVATED" }]<br>
                <input type="radio" name="confstrs[sFCPOSaveBankdata]" value="1" [{if $confstrs.sFCPOSaveBankdata == '1'}]checked[{/if}]> [{ oxmultilang ident="FCPO_ACTIVATED" }]
            </td>
            <td class="edittext">[{ oxinputhelp ident="FCPO_HELP_SAVEBANKDATA" }]</td>
        </tr>
        <tr><td colspan="3">&nbsp;</td></tr>
        
        
        <tr>
            <td colspan="3">
                <FIELDSET id=fldLayout>
                    <LEGEND id=lgdLayout>
                        [{if $languages}]
                            <select name="subjlang" class="editinput" onchange="Javascript:loadLang(this)" [{ $readonly}]>
                                [{foreach key=key item=item from=$languages}]
                                    <option value="[{$key}]"[{if $subjlang == $key}] SELECTED[{/if}]>[{$item->name}]</option>
                                [{/foreach}]
                            </select>
                        [{/if}]
                    </LEGEND>

                    <table cellspacing="0" cellpadding="1" border="0">
                        <tr>
                            <td class="edittext" >
                                [{ oxmultilang ident="FCPO_APPROVALTEXT"}]<br>
                                <textarea name="confstrs[sFCPOApprovalText]" rows="5" cols="120">[{$confstrs.sFCPOApprovalText}]</textarea>
                            </td>
                        </tr>
                        <tr><td>&nbsp;</td></tr>
                        <tr>
                            <td class="edittext" >
                                [{ oxmultilang ident="FCPO_DENIALTEXT"}]<br>
                                <textarea name="confstrs[sFCPODenialText]" rows="5" cols="120">[{$confstrs.sFCPODenialText}]</textarea>
                            </td>
                        </tr>
                    </table>
                </FIELDSET>
            </td>
        </tr>
        <tr><td colspan="3">&nbsp;</td></tr>
        <tr>
            <td class="edittext" colspan="3">
                <input type="submit" class="edittext" name="save" value="[{ oxmultilang ident="GENERAL_SAVE" }]" onClick="Javascript:document.myedit.fnc.value='save'" [{ $readonly}]>
            </td>
            <td></td>
        </tr>
        <tr><td colspan="3">&nbsp;</td></tr>
    </table>
</form>

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]