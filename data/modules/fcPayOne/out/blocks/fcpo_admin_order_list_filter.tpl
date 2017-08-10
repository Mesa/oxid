<td valign="top" class="listfilter first" height="20">
    <div class="r1"><div class="b1">
    <select name="folder" class="folderselect" onChange="document.search.submit();">
        <option value="-1" style="color: #000000;">[{oxmultilang ident="ORDER_LIST_FOLDER_ALL"}]</option>
        [{foreach from=$afolder key=field item=color}]
        <option value="[{$field}]" [{if $folder == $field}]SELECTED[{/if}] style="color: [{$color}];">[{oxmultilang ident=$field noerror=true}]</option>
        [{/foreach}]
    </select>
    <input autocomplete="off" class="listedit" type="text" size="15" maxlength="128" name="where[oxorder][oxorderdate]" value="[{$where.oxorder.oxorderdate|oxformdate}]" [{include file="help.tpl" helpid=order_date}]>
    </div></div>
</td>
<td valign="top" class="listfilter" height="20">
    <div class="r1"><div class="b1">
    <select name="addsearchfld" class="folderselect" >
        <option value="-1" style="color: #000000;">[{oxmultilang ident="ORDER_LIST_PAID"}]</option>
        [{foreach from=$asearch key=table item=desc}]
        [{assign var="ident" value=ORDER_SEARCH_FIELD_$desc}]
        [{assign var="ident" value=$ident|oxupper}]
        <option value="[{$table}]" [{if $addsearchfld == $table}]SELECTED[{/if}]>[{oxmultilang|oxtruncate:20:"..":true ident=$ident}]</option>
        [{/foreach}]
    </select>
    <input autocomplete="off" class="listedit" type="text" size="15" maxlength="128" name="addsearch" value="[{$addsearch}]">
    </div></div>
</td>
<td valign="top" class="listfilter" height="20">
    <div class="r1"><div class="b1">
    <input autocomplete="off" class="listedit" type="text" size="7" maxlength="128" name="where[oxorder][oxordernr]" value="[{$where.oxorder.oxordernr}]">
    </div></div>
</td>
<td valign="top" class="listfilter" height="20">
    <div class="r1"><div class="b1">
    <input autocomplete="off" class="listedit" type="text" size="25" maxlength="128" name="where[oxorder][oxbillfname]" value="[{$where.oxorder.oxbillfname}]">
    </div></div>
</td>
<td valign="top" class="listfilter" height="20">
    <div class="r1"><div class="b1">
	<input autocomplete="off" class="listedit" type="text" size="25" maxlength="128" name="where[oxorder][oxbilllname]" value="[{$where.oxorder.oxbilllname}]">
    </div></div>
</td>
<td valign="top" class="listfilter" height="20" colspan="2" nowrap>
    <div class="r1"><div class="b1">
    <div class="find"><input class="listedit" type="submit" name="submitit" value="[{oxmultilang ident="GENERAL_SEARCH"}]"></div>
    <!-- FCPAYONE BEGIN -->
    <input autocomplete="off" class="listedit" type="text" size="7" maxlength="128" name="where[oxorder][fcporefnr]" value="[{$where.oxorder.fcporefnr}]">
    <script type="text/javascript">
    <!--
    function FCPOdeleteThisOrder(sID)
    {
        var blCheck = confirm("[{oxmultilang ident="FCPO_ORDER_LIST_YOUWANTTODELETE"}]");
        if( blCheck == true ) {
            var oTransfer = top.basefrm.edit.document.getElementById( "transfer" );
            oTransfer.oxid.value = '-1';
            oTransfer.cl.value = top.oxid.admin.getClass( -1 );

            //forcing edit frame to reload after submit
            top.forceReloadingEditFrame();

            var oSearch = top.basefrm.list.document.getElementById( "search" );
            oSearch.oxid.value = sID;
            oSearch.fnc.value = 'deleteentry';
            oSearch.submit();
        }
    }
    //-->
    </script>
    <!-- FCPAYONE END -->
    </div></div>
</td>