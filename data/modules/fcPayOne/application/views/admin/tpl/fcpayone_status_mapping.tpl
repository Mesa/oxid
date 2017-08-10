[{include file="headitem.tpl" title="fcpo_admin_config_status_mapping"|oxmultilangassign}]

<form autocomplete="off" method="post" action="[{ $shop->selflink }]">
	[{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="cl" value="fcpayone_status_mapping" />
    <input type="hidden" name="fnc" value="save" />

    <h2>[{oxmultilang ident="fcpo_admin_config_status_mapping"}]</h2>
    
    [{assign var=oMappings value=$oView->getMappings()}]
    [{if $oMappings|@count > 0}]
        <br>
        <table>        
            <tr>
                <th style="text-align: left;">[{ oxmultilang ident="fcpo_admin_config_paymenttype" }]</th>
                <th style="text-align: left;">[{ oxmultilang ident="fcpo_admin_config_status_payone" }]</th>
                <th style="text-align: left;">[{ oxmultilang ident="fcpo_admin_config_status_shop" }]</th>
                <th></th>
            </tr>
            [{foreach from=$oMappings item=oMapping}]
                <tr>
                    <td>
                        <select name="editval[[{$oMapping->sOxid}]][sPaymentType]" style="width:200px;">
                            [{if $oMapping->sOxid == 'new'}]
                                <option value="">---</option>
                            [{/if}]
                            [{foreach from=$oView->getPaymentTypeList() item=oPayment}]
                                <option value="[{$oPayment->sId}]" [{if $oMapping->sPaymentType == $oPayment->sId}]selected[{/if}]>[{$oPayment->sTitle}]</option>
                            [{/foreach}]
                        </select>
                    </td>
                    <td>
                        <select name="editval[[{$oMapping->sOxid}]][sPayoneStatus]" style="width:325px;">
                            [{if $oMapping->sOxid == 'new'}]
                                <option value="">---</option>
                            [{/if}]
                            [{foreach from=$oView->getPayoneStatusList() item=oStatus}]
                                <option value="[{$oStatus->sId}]" [{if $oMapping->sPayoneStatusId == $oStatus->sId}]selected[{/if}]>[{$oStatus->sTitle}]</option>
                            [{/foreach}]
                        </select>
                    </td>
                    <td>
                        <select name="editval[[{$oMapping->sOxid}]][sShopStatus]" style="width:180px;">
                            [{if $oMapping->sOxid == 'new'}]
                                <option value="">---</option>
                            [{/if}]
                            [{foreach from=$oView->getShopStatusList() key=sStatusId item=sColor}]
                                <option value="[{$sStatusId}]" [{if $oMapping->sShopStatusId == $sStatusId}]selected[{/if}]>[{ oxmultilang ident=$sStatusId noerror=true }]</option>
                            [{/foreach}]
                        </select>
                    </td>
                    <td>
                        <input value="X [{ oxmultilang ident="fcpo_admin_config_delete" }]" name="editval[[{$oMapping->sOxid}]][delete]" onclick="if(!confirm('[{ oxmultilang ident="fcpo_admin_config_delete_confirm" }]')) {return false;}" type="submit">
                    </td>
                </tr>
            [{/foreach}]
        </table>
        <br>
        <input type="submit" name="save" value="[{ oxmultilang ident="GENERAL_SAVE" }]" />
    [{/if}]
    <input type="submit" name="add" value="[{ oxmultilang ident="fcpo_admin_config_add" }]" />
</form>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]