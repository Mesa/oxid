[{include file="headitem.tpl" title="fcpo_admin_config_status_forwarding"|oxmultilangassign}]

<form autocomplete="off" method="post" action="[{ $shop->selflink }]">
	[{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="cl" value="fcpayone_status_forwarding" />
    <input type="hidden" name="fnc" value="save" />

    <h2>[{oxmultilang ident="fcpo_admin_config_status_forwarding"}]</h2>

    [{assign var=oForwardings value=$oView->getForwardings()}]
    [{if $oForwardings|@count > 0}]
        <br>
        <table>
            <tr>
                <th style="text-align: left;">[{ oxmultilang ident="fcpo_admin_config_status" }]</th>
                <th style="text-align: left;">[{ oxmultilang ident="fcpo_admin_config_url" }]</th>
                <th style="text-align: left;">[{ oxmultilang ident="fcpo_admin_config_timeout" }]</th>
                <th></th>
            </tr>
            [{foreach from=$oForwardings item=oForwarding}]
                <tr>
                    <td>
                        <select name="editval[[{$oForwarding->sOxid}]][sPayoneStatus]" style="width:325px;">
                            [{if $oForwarding->sOxid == 'new'}]
                                <option value="">---</option>
                            [{/if}]
                            [{foreach from=$oView->getPayoneStatusList() item=oStatus}]
                                <option value="[{$oStatus->sId}]" [{if $oForwarding->sPayoneStatusId == $oStatus->sId}]selected[{/if}]>[{$oStatus->sTitle}]</option>
                            [{/foreach}]
                        </select>
                    </td>
                    <td>
                        <input autocomplete="off" type="text" size="55" name="editval[[{$oForwarding->sOxid}]][sForwardingUrl]" value="[{$oForwarding->sForwardingUrl}]">
                    </td>
                    <td>
                        <input autocomplete="off" type="text" size="5" name="editval[[{$oForwarding->sOxid}]][iForwardingTimeout]" value="[{$oForwarding->iForwardingTimeout}]">
                    </td>
                    <td>
                        <input value="X [{ oxmultilang ident="fcpo_admin_config_delete" }]" name="editval[[{$oForwarding->sOxid}]][delete]" onclick="if(!confirm('[{ oxmultilang ident="fcpo_admin_config_delete_confirm" }]')) {return false;}" type="submit">
                    </td>
                </tr>
            [{/foreach}]
        </table>
        <br>
        <input type="submit" value="[{ oxmultilang ident="GENERAL_SAVE" }]" />
    [{/if}]
    <input type="submit" name="add" value="[{ oxmultilang ident="fcpo_admin_config_add" }]" />
</form>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]