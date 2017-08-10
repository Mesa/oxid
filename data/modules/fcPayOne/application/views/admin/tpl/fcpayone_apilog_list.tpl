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

//-->
</script>

<div id="liste">
    <form autocomplete="off" name="search" id="search" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{include file="_formparams.tpl" cl="fcpayone_apilog_list" lstrt=$lstrt actedit=$actedit oxid=$oxid fnc="" language=$actlang editlanguage=$actlang}]

        <table cellspacing="0" cellpadding="0" border="0" width="100%">
            <colgroup>
                <col width="25%">
                <col width="25%">
                <col width="25%">
                <col width="24%">
                <col width="1%">
            </colgroup>
            <tr class="listitem">
                <td valign="top" class="listfilter">
                    <div class="r1"><div class="b1">
                        <input class="listedit" type="text" size="30" maxlength="128" name="[{$oView->fcGetInputName('fcporequestlog', 'fcpo_timestamp')}]" value="[{$oView->fcGetWhereValue('fcporequestlog', 'fcpo_timestamp')}]">
                    </div></div>
                </td>
                <td valign="top" class="listfilter">
                    <div class="r1"><div class="b1">
                        &nbsp;
                    </div></div>
                </td>
                <td valign="top" class="listfilter">
                    <div class="r1"><div class="b1">
                        <input class="listedit" type="text" size="30" maxlength="128" name="[{$oView->fcGetInputName('fcporequestlog', 'fcpo_request')}]" value="[{$oView->fcGetWhereValue('fcporequestlog', 'fcpo_request')}]"><br>
                    </div></div>
                </td>
                <td valign="top" class="listfilter" colspan="2" nowrap>
                    <div class="r1"><div class="b1">
                        <div class="find"><input class="listedit" type="submit" name="submitit" value="[{ oxmultilang ident="GENERAL_SEARCH" }]"></div>
                    <input class="listedit" type="text" size="30" maxlength="128" name="[{$oView->fcGetInputName('fcporequestlog', 'fcpo_response')}]" value="[{$oView->fcGetWhereValue('fcporequestlog', 'fcpo_response')}]">
                    </div></div>
                </td>
            </tr>
            <tr>
                <td class="listheader first" height="15">&nbsp;<a href="[{$oView->fcGetSortingJavascript('fcporequestlog', 'fcpo_timestamp')}]" class="listheader">[{ oxmultilang ident="FCPO_LIST_HEADER_TIMESTAMP" }]</a></td>
                <td class="listheader">[{ oxmultilang ident="FCPO_CHANNEL" }]</td>
                <td class="listheader"><a href="[{$oView->fcGetSortingJavascript('fcporequestlog', 'fcpo_requesttype')}]" class="listheader">[{ oxmultilang ident="FCPO_LIST_HEADER_REQUEST" }]</a></td>
                <td class="listheader" colspan="2"><a href="[{$oView->fcGetSortingJavascript('fcporequestlog', 'fcpo_responsestatus')}]" class="listheader">[{ oxmultilang ident="FCPO_LIST_HEADER_RESPONSE" }]</a></td>
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
                    <td valign="top" class="[{ $listclass}]" height="15"><div class="listitemfloating">&nbsp;<a href="Javascript:top.oxid.admin.editThis('[{ $listitem->fcporequestlog__oxid->value}]');" class="[{ $listclass}]">[{ $listitem->fcporequestlog__fcpo_timestamp->value }]</a></div></td>
                    <td valign="top" class="[{ $listclass}]"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->fcporequestlog__oxid->value }]');" class="[{ $listclass}]">Serverapi</a></div></td>
                    <td valign="top" class="[{ $listclass}]"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->fcporequestlog__oxid->value }]');" class="[{ $listclass}]">[{ $listitem->fcporequestlog__fcpo_requesttype->value }]</a></div></td>
                    <td valign="top" class="[{ $listclass}]"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{ $listitem->fcporequestlog__oxid->value }]');" class="[{ $listclass}]">[{ $listitem->fcporequestlog__fcpo_responsestatus->value }]</a></div></td>
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
    parent.parent.sMenuSubItem = "[{ oxmultilang ident="fcpo_admin_api_logs" }]";
    parent.parent.sWorkArea    = "[{$_act}]";
    parent.parent.setTitle();
}
</script>
</body>
</html>