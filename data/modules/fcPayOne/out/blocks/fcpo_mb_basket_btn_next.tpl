[{if $oView->fcpoGetPayPalExpressPic()}]
    <div style="text-align:center;">
        <form autocomplete="off" action="[{$oViewConf->getSslSelfLink()}]" method="post">
            [{$oViewConf->getHiddenSid()}]
            <input type="hidden" name="cl" value="basket">
            <input type="hidden" name="fnc" value="fcpoUsePayPalExpress">
            <input type="image" src="[{$oView->fcpoGetPayPalExpressPic()}]">
        </form>
        <span>[{oxmultilang ident="FCPO_OR"}]</span>
    </div>
[{/if}]
[{$smarty.block.parent}]