[{$smarty.block.parent}]
[{if $oView->fcpoIsMandateError()}]
    [{include file="message/error.tpl" statusMessage="FCPO_ORDER_MANDATE_ERROR"|oxmultilangassign}]
[{/if}]