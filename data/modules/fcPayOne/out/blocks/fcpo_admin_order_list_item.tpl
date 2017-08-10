[{if $listitem->oxorder__oxstorno->value == 1}]
    [{assign var="listclass" value=listitem3}]
[{else}]
    [{if $listitem->blacklist == 1}]
        [{assign var="listclass" value=listitem3}]
    [{else}]
        [{assign var="listclass" value=listitem$blWhite}]
    [{/if}]
[{/if}]
[{if $listitem->getId() == $oxid}]
    [{assign var="listclass" value=listitem4}]
[{/if}]
<td valign="top" class="[{$listclass}]" height="15"><div class="listitemfloating">&nbsp;<a href="Javascript:top.oxid.admin.editThis('[{$listitem->oxorder__oxid->value}]');" class="[{$listclass}]">[{$listitem->oxorder__oxorderdate|oxformdate:'datetime':true}]</a></div></td>
<td valign="top" class="[{$listclass}]" height="15"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{$listitem->oxorder__oxid->value}]');" class="[{$listclass}]">[{$listitem->oxorder__oxpaid|oxformdate}]</a></div></td>
<td valign="top" class="[{$listclass}]" height="15"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{$listitem->oxorder__oxid->value}]');" class="[{$listclass}]">[{$listitem->oxorder__oxordernr->value}]</a></div></td>
<td valign="top" class="[{$listclass}]" height="15"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{$listitem->oxorder__oxid->value}]');" class="[{$listclass}]">[{$listitem->oxorder__oxbillfname->value}]</a></div></td>
<td valign="top" class="[{$listclass}]" height="15"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{$listitem->oxorder__oxid->value}]');" class="[{$listclass}]">[{$listitem->oxorder__oxbilllname->value}]</a></div></td>
<!-- FCPAYONE BEGIN -->
<td valign="top" class="[{$listclass}]" height="15"><div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{$listitem->oxorder__oxid->value}]');" class="[{$listclass}]">[{$listitem->oxorder__fcporefnr->value}]</a></div></td>
<!-- FCPAYONE END -->
<td class="[{$listclass}]">
    [{if !$readonly}]
        <!-- FCPAYONE BEGIN -->
        <a href="Javascript:FCPOdeleteThisOrder('[{$listitem->oxorder__oxid->value}]');" class="delete" id="del.[{$_cnt}]" [{include file="help.tpl" helpid=item_delete}]></a>
        <!-- FCPAYONE END -->
        <a href="Javascript:StornoThisArticle('[{$listitem->oxorder__oxid->value}]');" class="pause" id="pau.[{$_cnt}]" [{include file="help.tpl" helpid=item_storno}]></a>
    [{/if}]</td>
</td>