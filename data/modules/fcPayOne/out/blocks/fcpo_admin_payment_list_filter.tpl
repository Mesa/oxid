<td valign="top" class="listfilter first" height="20" colspan="2">
    <div class="r1"><div class="b1">
    <div class="find">
        <select name="changelang" class="editinput" onChange="Javascript:top.oxid.admin.changeLanguage();">
        [{foreach from=$languages item=lang}]
        <option value="[{$lang->id}]" [{if $lang->selected}]SELECTED[{/if}]>[{$lang->name}]</option>
        [{/foreach}]
        </select>
         <input class="listedit" type="submit" name="submitit" value="[{oxmultilang ident="GENERAL_SEARCH"}]">
    </div>

    <input autocomplete="off" class="listedit" type="text" size="60" maxlength="128" name="where[oxpayments][oxdesc]" value="[{$where.oxpayments.oxdesc}]">
    <!-- FCPAYONE BEGIN -->
    | [{oxmultilang ident="FCPO_ONLY_PAYONE"}] <input class="edittext" onchange="this.form.submit();" type="checkbox" name="where[oxpayments][fcpoispayone]" value='1' [{if $where.oxpayments.fcpoispayone == 1}]checked[{/if}]>
    <!-- FCPAYONE END -->
    </div></div>
</td>