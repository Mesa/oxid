[{include file="headitem.tpl" title="SYSREQ_MAIN_TITLE"|oxmultilangassign box="list"}]
[{assign var="where" value=$oView->getListFilter()}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<script type="text/javascript">
<!--
window.onload = function ()
{
    top.reloadEditFrame();
    [{ if $updatelist == 1}]
        top.oxid.admin.updateList('[{ $oxid }]');
    [{ /if}]
}

function editThisStatus( sOxid ) {
    var oTransfer = top.basefrm.edit.document.getElementById( "transfer" );
    oTransfer.oxid.value = sOxid;
    oTransfer.cl.value = 'fcpayone_log';

    //forcing edit frame to reload after submit
    top.forceReloadingEditFrame();

    var oSearch = top.basefrm.list.document.getElementById( "search" );
    oSearch.oxid.value = sOxid;
    oSearch.actedit.value = 1;
    oSearch.submit();
}
//-->
</script>

<div id="liste">
    <form autocomplete="off" name="search" id="search" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{include file="_formparams.tpl" cl="fcpayone_log_list" lstrt=$lstrt actedit=$actedit oxid=$oxid fnc="" language=$actlang editlanguage=$actlang}]

        <table cellspacing="0" cellpadding="0" border="0" width="100%">
            <colgroup>
                <col width="20%">
                <col width="20%">
                <col width="19%">
                <col width="10%">
                <col width="10%">
                <col width="10%">
                <col width="10%">
                <col width="1%">
            </colgroup>
            <tr class="listitem">
                <td valign="top" class="listfilter first" height="20">
                    <div class="r1"><div class="b1">
                        <input class="listedit" type="text" size="20" maxlength="128" name="[{$oView->fcGetInputName('fcpotransactionstatus', 'fcpo_timestamp')}]" value="[{$oView->fcGetWhereValue('fcpotransactionstatus', 'fcpo_timestamp')}]">
                    </div></div>
                </td>
                <td valign="top" class="listfilter">
                    <div class="r1"><div class="b1">
                        <input class="listedit" type="text" size="20" maxlength="128" name="[{$oView->fcGetInputName('fcpotransactionstatus', 'fcpo_ordernr')}]" value="[{$oView->fcGetWhereValue('fcpotransactionstatus', 'fcpo_ordernr')}]">
                    </div></div>
                </td>
                <td valign="top" class="listfilter">
                    <div class="r1"><div class="b1">
                        <input class="listedit" type="text" size="20" maxlength="128" name="[{$oView->fcGetInputName('fcpotransactionstatus', 'fcpo_txid')}]" value="[{$oView->fcGetWhereValue('fcpotransactionstatus', 'fcpo_txid')}]">
                    </div></div>
                </td>
                <td valign="top" class="listfilter">
                    <div class="r1"><div class="b1">
                        <input class="listedit" type="text" size="10" maxlength="128" name="[{$oView->fcGetInputName('fcpotransactionstatus', 'fcpo_clearingtype')}]" value="[{$oView->fcGetWhereValue('fcpotransactionstatus', 'fcpo_clearingtype')}]">
                    </div></div>
                </td>
                <td valign="top" class="listfilter">
                    <div class="r1"><div class="b1">
                        <input class="listedit" type="text" size="20" maxlength="128" name="[{$oView->fcGetInputName('fcpotransactionstatus', 'fcpo_email')}]" value="[{$oView->fcGetWhereValue('fcpotransactionstatus', 'fcpo_email')}]">
                    </div></div>
                </td>
                <td valign="top" class="listfilter">
                    <div class="r1"><div class="b1">
                        <input class="listedit" type="text" size="15" maxlength="128" name="[{$oView->fcGetInputName('fcpotransactionstatus', 'fcpo_price')}]" value="[{$oView->fcGetWhereValue('fcpotransactionstatus', 'fcpo_price')}]">
                    </div></div>
                </td>
                <td valign="top" class="listfilter" colspan="2" nowrap>
                    <div class="r1"><div class="b1">
                        <div class="find"><input class="listedit" type="submit" name="submitit" value="[{ oxmultilang ident="GENERAL_SEARCH" }]"></div>
                    <input class="listedit" type="text" size="5" maxlength="128" name="[{$oView->fcGetInputName('fcpotransactionstatus', 'fcpo_txaction')}]" value="[{$oView->fcGetWhereValue('fcpotransactionstatus', 'fcpo_txaction')}]">
                    </div></div>
                </td>
            </tr>
            <tr>
                <td class="listheader first" height="15">&nbsp;<a href="[{$oView->fcGetSortingJavascript('fcpotransactionstatus', 'fcpo_timestamp')}]" class="listheader">[{ oxmultilang ident="FCPO_LIST_HEADER_TXTIME" }]</a></td>
                <td class="listheader"><a href="[{$oView->fcGetSortingJavascript('fcpotransactionstatus', 'fcpo_ordernr')}]" class="listheader">[{ oxmultilang ident="FCPO_LIST_HEADER_ORDERNR" }]</a></td>
                <td class="listheader"><a href="[{$oView->fcGetSortingJavascript('fcpotransactionstatus', 'fcpo_txid')}]" class="listheader">[{ oxmultilang ident="FCPO_LIST_HEADER_TXID" }]</a></td>
                <td class="listheader"><a href="[{$oView->fcGetSortingJavascript('fcpotransactionstatus', 'fcpo_clearingtype')}]" class="listheader">[{ oxmultilang ident="FCPO_LIST_HEADER_CLEARINGTYPE" }]</a></td>
                <td class="listheader"><a href="[{$oView->fcGetSortingJavascript('fcpotransactionstatus', 'fcpo_email')}]" class="listheader">[{ oxmultilang ident="FCPO_LIST_HEADER_EMAIL" }]</a></td>
                <td class="listheader"><a href="[{$oView->fcGetSortingJavascript('fcpotransactionstatus', 'fcpo_price')}]" class="listheader">[{ oxmultilang ident="FCPO_LIST_HEADER_PRICE" }]</a></td>
                <td class="listheader" colspan="2"><a href="[{$oView->fcGetSortingJavascript('fcpotransactionstatus', 'fcpo_txaction')}]" class="listheader">[{ oxmultilang ident="FCPO_LIST_HEADER_TXACTION" }]</a></td>
            </tr>

            [{assign var="blWhite" value=""}]
            [{assign var="_cnt" value=0}]
            [{foreach from=$mylist item=listitem}]
                [{assign var="_cnt" value=$_cnt+1}]
                <tr id="row.[{$_cnt}]">
                    [{ if $listitem->blacklist == 1}]
                        [{assign var="listclass" value=listitem3 }]
                    [{ else}]
                        [{assign var="listclass" value=listitem$blWhite }]
                    [{ /if}]
                    [{ if $listitem->getId() == $oxid }]
                        [{assign var="listclass" value=listitem4 }]
                    [{ /if}]
                    <td valign="top" class="[{ $listclass}]" height="15"><div class="listitemfloating">&nbsp;<a href="Javascript:top.oxid.admin.editThis('[{ $listitem->fcpotransactionstatus__oxid->value}]');" class="[{ $listclass}]">[{ $listitem->fcpotransactionstatus__fcpo_timestamp->value }]</a></div></td>
                    <td valign="top" class="[{ $listclass}]"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->fcpotransactionstatus__oxid->value }]');" class="[{ $listclass}]">[{ $listitem->fcpotransactionstatus__fcpo_ordernr->value }]</a></div></td>
                    <td valign="top" class="[{ $listclass}]"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->fcpotransactionstatus__oxid->value }]');" class="[{ $listclass}]">[{ $listitem->fcpotransactionstatus__fcpo_txid->value }]</a></div></td>
                    <td valign="top" class="[{ $listclass}]"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->fcpotransactionstatus__oxid->value }]');" class="[{ $listclass}]">[{ $listitem->fcpotransactionstatus__fcpo_clearingtype->value }]</a></div></td>
                    <td valign="top" class="[{ $listclass}]"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->fcpotransactionstatus__oxid->value }]');" class="[{ $listclass}]">[{ $listitem->fcpotransactionstatus__fcpo_email->value }]</a></div></td>
                    <td valign="top" class="[{ $listclass}]"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->fcpotransactionstatus__oxid->value }]');" class="[{ $listclass}]">[{ $listitem->fcpotransactionstatus__fcpo_price->value|number_format:2:',':'' }] [{$listitem->fcpotransactionstatus__fcpo_currency->value}]</a></div></td>
                    <td valign="top" class="[{ $listclass}]"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->fcpotransactionstatus__oxid->value }]');" class="[{ $listclass}]">[{ $listitem->fcpotransactionstatus__fcpo_txaction->value }]</a></div></td>
                    <td class="[{ $listclass}]"></td>
                </tr>
                [{if $blWhite == "2"}]
                    [{assign var="blWhite" value=""}]
                [{else}]
                    [{assign var="blWhite" value="2"}]
                [{/if}]
            [{/foreach}]
            [{include file="pagenavisnippet.tpl" colspan="8"}]
        </table>
    </form>
</div>

[{include file="pagetabsnippet.tpl"}]


<script type="text/javascript">
if (parent.parent)
{   parent.parent.sShopTitle   = "[{$actshopobj->oxshops__oxname->getRawValue()|oxaddslashes}]";
    parent.parent.sMenuItem    = "[{ oxmultilang ident="fcpo_admin_title" }]";
    parent.parent.sMenuSubItem = "[{ oxmultilang ident="fcpo_main_log" }]";
    parent.parent.sWorkArea    = "[{$_act}]";
    parent.parent.setTitle();
}
</script>
</body>
</html>