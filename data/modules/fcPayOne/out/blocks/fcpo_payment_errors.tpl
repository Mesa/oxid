[{$smarty.block.parent}]

[{assign var="iPayError" value=$oView->getPaymentError()}]
[{if $iPayError == -20}]
    [{if method_exists($oViewConf, 'getActiveTheme') && $oViewConf->getActiveTheme() == 'mobile'}]
        <div class="payment-row">
            <div class="alert alert-error">[{$oView->getPaymentErrorText()}]</div>
        </div>    
    [{elseif method_exists($oViewConf, 'getActiveTheme') && $oViewConf->getActiveTheme() == 'flow'}]
        <div class="alert alert-danger">[{$oView->getPaymentErrorText()}]</div>
    [{else}]
        <div class="status error">[{$oView->getPaymentErrorText()}]</div>
    [{/if}]
[{/if}]

