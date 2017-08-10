[{$smarty.block.parent}]
[{assign var="payment" value=$oView->getPayment()}]
[{assign var="sMandateText" value=$payment->fcpoGetMandateText()}]
[{if $payment && method_exists($payment, 'fcpoGetMandateText') && $payment->fcpoGetMandateText()}]
    [{oxscript include=$oViewConf->fcpoGetModuleJsPath('fcPayOne.js')}]
    [{oxscript include=$oViewConf->fcpoGetModuleCssPath('fcpayone.css')}]
    <div id="fcpoSEPAMandate">
        [{if method_exists($oViewConf, 'getActiveTheme') && $oViewConf->getActiveTheme() == 'mobile'}]
            <h3 class="heading section-heading">
                <span>SEPA-Lastschrift</span>
            </h3>
        [{else}]
            <h3 class="section">
                <strong>SEPA-Lastschrift</strong>
            </h3>
        [{/if}]
        [{oxmultilang ident="FCPO_ORDER_MANDATE_INFOTEXT"}]
        <div class="fcpoSEPAMandate">
            [{$sMandateText}]
        </div>
        
        <div class="fcpoSEPAMandateCheckbox[{if method_exists($oViewConf, 'getActiveTheme') && $oViewConf->getActiveTheme() == 'mobile'}] fcpoMobile[{/if}]">
            <label style="float:left; padding-right:10px;" for="mandate_status" class="control-label">[{oxmultilang ident="FCPO_ORDER_MANDATE_CHECKBOX"}]</label>
            <input type="checkbox" onclick="fcpoHandleMandateCheckbox(this);">
            <div class="clear"></div>
        </div>
    </div>
[{/if}]