<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
    <title>[{ oxmultilang ident="GENERAL_ADMIN_TITLE_1" }]</title>
</head>

<script type="text/javascript">
    if (parent.parent)
    {   parent.parent.sShopTitle   = "[{$actshopobj->oxshops__oxname->getRawValue()|oxaddslashes}]";
        parent.parent.sMenuItem    = "[{ oxmultilang ident="fcpo_admin_title" }]";
        parent.parent.sMenuSubItem = "[{ oxmultilang ident="fcpo_admin_common" }]";
        parent.parent.sWorkArea    = "[{$_act}]";
        parent.parent.setTitle();
    }
</script>

<!-- frames -->
<frameset  rows="100%" border="0">
    <frame src="https://www.payone.de/shopplugins/oxid/embedded.html?integratorid=[{$oView->fcpoGetIntegratorId()}]&integratorver=[{$edition}][{$version}]&integratorextver=[{$oView->fcpoGetVersion()}]&mid=[{$oView->fcpoGetMerchantId()}]" name="edit" marginwidth="0" marginheight="0" scrolling="auto" frameborder="0">
</frameset>


</html>