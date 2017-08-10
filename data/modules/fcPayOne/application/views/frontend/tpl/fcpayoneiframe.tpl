[{capture append="oxidBlock_content"}]
    [{* ordering steps *}]
    [{include file="page/checkout/inc/steps.tpl" active=4}]
    
    [{if $oView->getIframeHeader()}]<h3 class="blockHead" id="paymentHeader">[{$oView->getIframeHeader()}]</h3>[{/if}]
    
    [{if $oView->getIframeText()}][{$oView->getIframeText()}][{/if}]
    <div style="clear:both;"></div>
    <iframe 
        src="[{$oView->getIframeUrl()}]" 
        [{if $oView->getIframeHeight()}]height="[{$oView->getIframeHeight()}]"[{/if}]
        [{if $oView->getIframeWidth()}]width="[{$oView->getIframeWidth()}]"[{/if}]
        [{if $oView->getIframeStyle()}]style="[{$oView->getIframeStyle()}]"[{/if}]
        >
    </iframe>
    
[{/capture}]
[{include file="layout/page.tpl"}]
