[{if $payment->oxuserpayments__oxpaymentsid->value == "oxidpayadvance"}]
[{oxmultilang ident="FCPO_EMAIL_BANK"}] [{$shop->oxshops__oxbankname->getRawValue()}]<br>
[{oxmultilang ident="FCPO_EMAIL_ROUTINGNUMBER"}] [{$shop->oxshops__oxbankcode->value}]<br>
[{oxmultilang ident="FCPO_EMAIL_ACCOUNTNUMBER"}] [{$shop->oxshops__oxbanknumber->value}]<br>
[{oxmultilang ident="FCPO_EMAIL_BIC"}] [{$shop->oxshops__oxbiccode->value}]<br>
[{oxmultilang ident="FCPO_EMAIL_IBAN"}] [{$shop->oxshops__oxibannumber->value}]
[{* FCPAYONE BEGIN *}]
[{elseif $payment->oxuserpayments__oxpaymentsid->value == "fcpopayadvance"}]
[{oxmultilang ident="FCPO_BANKACCOUNTHOLDER"}] [{$order->getFcpoBankaccountholder()}]
[{oxmultilang ident="FCPO_EMAIL_BANK"}] [{$order->getFcpoBankname()}]
[{oxmultilang ident="FCPO_EMAIL_ROUTINGNUMBER"}] [{$order->getFcpoBankcode()}]
[{oxmultilang ident="FCPO_EMAIL_ACCOUNTNUMBER"}] [{$order->getFcpoBanknumber()}]
[{oxmultilang ident="FCPO_EMAIL_BIC"}] [{$order->getFcpoBiccode()}]
[{oxmultilang ident="FCPO_EMAIL_IBAN"}] [{$order->getFcpoIbannumber()}]
[{* FCPAYONE END *}]
[{elseif $payment->oxuserpayments__oxpaymentsid->value == "fcpopo_bill" || $payment->oxuserpayments__oxpaymentsid->value == "fcpopo_debitnote"}]    
[{oxmultilang ident="FCPO_PAYOLUTION_EMAIL_CLEARING"}]: [{$smarty.session.payolution_clearing}]
[{else}]
[{$smarty.block.parent}]
[{/if}]