[{include file="headitem.tpl" title="fcpo_admin_config_status_mapping"|oxmultilangassign}]

<form autocomplete="off" method="post" action="[{$shop->selflink}]">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="cl" value="fcpayone_error_mapping" />
    <input type="hidden" name="fnc" value="save" />

    <h2>[{oxmultilang ident="fcpo_admin_config_error_mapping"}]</h2>
    
    [{assign var=oMappings value=$oView->getMappings()}]
    [{if $oMappings|@count > 0}]
        <br>
        <table>        
            <tr>
                <th style="text-align: left;">[{oxmultilang ident="fcpo_admin_config_payone_error_message"}]</th>
                <th style="text-align: left;">[{oxmultilang ident="fcpo_admin_config_status_language"}]</th>
                <th style="text-align: left;">[{oxmultilang ident="fcpo_admin_config_status_own_error_message"}]</th>
                <th></th>
            </tr>
            [{foreach from=$oMappings item=oMapping}]
                <tr>
                    <td>
                        <select name="editval[[{$oMapping->sOxid}]][sErrorCode]" style="width:200px;">
                            [{if $oMapping->sOxid == 'new'}]
                                <option value="">---</option>
                            [{/if}]
                            [{foreach from=$oView->fcpoGetPayoneErrorMessages() item=oError}]
                                <option value="[{$oError->sErrorCode}]" [{if $oMapping->sErrorCode == $oError->sErrorCode}]selected[{/if}]>[{$oError->sErrorMessage}] ([{$oError->sErrorCode}])</option>
                            [{/foreach}]
                        </select>
                    </td>
                    <td>
                        <select name="editval[[{$oMapping->sOxid}]][sLangId]" style="width:180px;">
                            [{if $oMapping->sOxid == 'new'}]
                                <option value="">---</option>
                            [{/if}]
                            [{foreach from=$oView->getLanguages() item=oLang}]
                                <option value="[{$oLang->id}]" [{if $oLang->id == $oMapping->sLangId}]selected[{/if}]>[{$oLang->name}]</option>
                            [{/foreach}]
                        </select>
                    </td>
                    <td>
                        <input type="text" name="editval[[{$oMapping->sOxid}]][sMappedMessage]" value="[{$oMapping->sMappedMessage}]" style="width:350px;">
                    </td>
                    <td>
                        <input value="X [{oxmultilang ident="fcpo_admin_config_delete"}]" name="editval[[{$oMapping->sOxid}]][delete]" onclick="if(!confirm('[{oxmultilang ident="fcpo_admin_config_delete_confirm"}]')) {return false;}" type="submit">
                    </td>
                </tr>
            [{/foreach}]
        </table>
        <br>
        <input type="submit" name="save" value="[{oxmultilang ident="GENERAL_SAVE"}]" />
    [{/if}]
    <input type="submit" name="add" value="[{oxmultilang ident="fcpo_admin_config_add"}]" />
</form>
<form autocomplete="off" method="post" action="[{$shop->selflink}]">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="cl" value="fcpayone_error_mapping" />
    <input type="hidden" name="fnc" value="saveIframe" />

    <h2>[{oxmultilang ident="fcpo_admin_config_error_iframe_mapping"}]</h2>
    
    [{assign var=oIframeMappings value=$oView->getIframeMappings()}]
    [{if $oIframeMappings|@count > 0}]
        <br>
        <table>        
            <tr>
                <th style="text-align: left;">[{oxmultilang ident="fcpo_admin_config_payone_error_code"}]</th>
                <th style="text-align: left;">[{oxmultilang ident="fcpo_admin_config_status_language"}]</th>
                <th style="text-align: left;">[{oxmultilang ident="fcpo_admin_config_status_own_error_message"}]</th>
                <th></th>
            </tr>
            [{foreach from=$oIframeMappings item=oMapping}]
                <tr>
                    <td>
                        <select name="editval2[[{$oMapping->sOxid}]][sErrorCode]" style="width:200px;">
                            [{if $oMapping->sOxid == 'new'}]
                                <option value="">---</option>
                            [{/if}]
                            [{foreach from=$oView->fcpoGetPayoneErrorMessages('iframe') item=oError}]
                                <option value="[{$oError->sErrorCode}]" [{if $oMapping->sErrorCode == $oError->sErrorCode}]selected[{/if}]>[{$oError->sErrorCode}]</option>
                            [{/foreach}]
                        </select>
                    </td>
                    <td>
                        <select name="editval2[[{$oMapping->sOxid}]][sLangId]" style="width:180px;">
                            [{if $oMapping->sOxid == 'new'}]
                                <option value="">---</option>
                            [{/if}]
                            [{foreach from=$oView->getLanguages() item=oLang}]
                                <option value="[{$oLang->id}]" [{if $oLang->id == $oMapping->sLangId}]selected[{/if}]>[{$oLang->name}]</option>
                            [{/foreach}]
                        </select>
                    </td>
                    <td>
                        <input type="text" name="editval2[[{$oMapping->sOxid}]][sMappedMessage]" value="[{$oMapping->sMappedMessage}]" style="width:350px;">
                    </td>
                    <td>
                        <input value="X [{oxmultilang ident="fcpo_admin_config_delete"}]" name="editval2[[{$oMapping->sOxid}]][delete]" onclick="if(!confirm('[{oxmultilang ident="fcpo_admin_config_delete_confirm"}]')) {return false;}" type="submit">
                    </td>
                </tr>
            [{/foreach}]
        </table>
        <br>
        <input type="submit" name="save" value="[{oxmultilang ident="GENERAL_SAVE"}]" />
    [{/if}]
    <input type="submit" name="addIframe" value="[{oxmultilang ident="fcpo_admin_config_add"}]" />
</form>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]