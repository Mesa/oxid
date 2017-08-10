  <tr>
    <td>[{oxmultilang ident="ROLES_BEMAIN_ACTIVE"}]</td>
    <td>
        <input class="edittext" type="checkbox" name="editval[oxroles__oxactive]" value="1" [{if $edit->oxroles__oxactive->value}]checked[{/if}] [{$readonly}]>
        [{oxinputhelp ident="HELP_ROLES_BEMAIN_ACTIVE"}]
    </td>
  </tr>
  <tr>
    <td>[{oxmultilang ident="ROLES_BEMAIN_TITLE"}]</td>
    <td>
        <input autocomplete="off" class="edittext" type="text" style="width:215px" name="editval[oxroles__oxtitle]" maxlength="[{$edit->oxroles__oxtitle->fldmax_length}]" value="[{$edit->oxroles__oxtitle->value}]" [{$readonly}]>
        [{oxinputhelp ident="HELP_ROLES_BEMAIN_TITLE"}]
    </td>
  </tr>
  <tr>
    <td colspan="2">

<div style="position:relative">

<table class="edittext rrtable">
  <tr class="head">
    <td colspan="2">[{oxmultilang ident="ROLES_BEMAIN_UIROOTHEADER"}]</td>
    <td>[{oxmultilang ident="ROLES_BEMAIN_UIRIGHT_F"}]</td>
    <td>[{oxmultilang ident="ROLES_BEMAIN_UIRIGHT_R"}]</td>
    <td>[{oxmultilang ident="ROLES_BEMAIN_UIRIGHT_D"}]</td>
    <td>[{oxmultilang ident="ROLES_BEMAIN_UIRIGHT_CUST"}]</td>
  </tr>

  [{foreach from=$adminmenu item=oNode}]
    [{if $oNode->tagName != 'BTN'}]
        [{assign var="id" value=$oNode->getAttribute('id')}]
        [{if isset( $aRights.$id )}]
          [{assign var="idx" value=$aRights.$id}]
        [{else}]
          [{assign var="idx" value=2}]
        [{/if}]
        [{if $oNode->hasAttribute('idx') && $oNode->getAttribute('idx') < $idx}]
          [{assign var="idx" value=$oNode->getAttribute('idx')}]
        [{/if}]

        <tr id="[{$oNode->getAttribute('id')}]">
          <td>
            [{if $oNode->childNodes->length}]
              [{include file="roles_bemain_inc.tpl" aNodes=$oNode->childNodes oParent=$oNode iParentIdx=$idx}]
              <a href="#" onclick="JavaScript:openNode( this );return false;"> &raquo; </a>
            [{/if}]
          </td>
          <td class="title">
            [{oxmultilang ident=$oNode->getAttribute('id') noerror=true}]
          </td>
          <td>
            <input [{$readonly}] type="radio" [{if $oNode->hasAttribute('idx') && $oNode->getAttribute('idx') < 2}]disabled[{/if}] name="aFields[[{$oNode->getAttribute('id')}]]" onclick="JavaScript:setPerms( this );" value="2" [{if $idx == 2}]checked[{/if}]>
          </td>
          <td>
            <input [{$readonly}] type="radio" [{if $oNode->hasAttribute('idx') && $oNode->getAttribute('idx') < 1}]disabled[{/if}] name="aFields[[{$oNode->getAttribute('id')}]]" onclick="JavaScript:setPerms( this );" value="1" [{if $idx == 1}]checked[{/if}]>
          </td>
          <td>
            <input [{$readonly}] type="radio" name="aFields[[{$oNode->getAttribute('id')}]]" onclick="JavaScript:setPerms( this );" value="0" [{if !$idx}]checked[{/if}]>
          </td>
          <td>
            [{if $oNode->childNodes->length}]
              <input readonly disabled type="checkbox" id="aFields[[{$oNode->getAttribute('id')}]]_cust" value="0">
              <script type="text/javascript">
              <!--
                updateCustInfo(document.getElementById("aFields[[{$oNode->getAttribute('id')}]]_cust"));
              //-->
              </script>
            [{/if}]
          </td>
        </tr>
    [{/if}]
  [{/foreach}]

  [{* service area *}]

  [{if isset( $aDynRights.dyn_menu ) && $aDynRights.dyn_menu || !isset( $aDynRights.dyn_menu )}]
    [{if isset( $aRights.dyn_menu )}]
      [{assign var="iParentIdx" value=$aRights.dyn_menu}]
    [{else}]
      [{assign var="iParentIdx" value=2}]
    [{/if}]
    [{if isset( $aDynRights.dyn_menu ) && $aDynRights.dyn_menu < $iParentIdx}]
      [{assign var="iParentIdx" value=$aDynRights.dyn_menu}]
    [{/if}]

    <tr id="dyn_menu">
      <td>

        <table class="edittext rrtableabs" style="display:none">
        <tr class="head">
          <td colspan="2">[{oxmultilang ident='dyn_menu' noerror=true}]</td>
          <td>[{oxmultilang ident="ROLES_BEMAIN_UIRIGHT_F"}]</td>
          <td>[{oxmultilang ident="ROLES_BEMAIN_UIRIGHT_R"}]</td>
          <td>[{oxmultilang ident="ROLES_BEMAIN_UIRIGHT_D"}]</td>
          <td valign="middle">
            <div onclick="JavaScript:openNode( this.parentNode.parentNode.parentNode.parentNode );" class="closebutton">x</div>
          </td>
        </tr>

        [{if isset( $aRights.dyn_about )}]
          [{assign var="idx" value=$aRights.dyn_about}]
        [{else}]
          [{assign var="idx" value=2}]
        [{/if}]

        [{if isset( $aDynRights.dyn_about ) && $aDynRights.dyn_about < $idx}]
          [{assign var="idx" value=$aDynRights.dyn_menu}]
        [{elseif $iParentIdx < $idx}]
          [{assign var="idx" value=$iParentIdx}]
        [{/if}]

        <tr id="dyn_about">
          <td>
          </td>
          <td class="title">
            [{oxmultilang ident='dyn_about' noerror=true}]
          </td>
          <td>
            <input [{$readonly}] type="radio" [{if $iParentIdx < 2}]disabled[{/if}] name="aFields[dyn_about]" onclick="JavaScript:setPerms( this );" value="2" [{if $idx == 2}]checked[{/if}]>
          </td>
          <td>
            <input [{$readonly}] type="radio" [{if $iParentIdx < 1}]disabled[{/if}] name="aFields[dyn_about]" onclick="JavaScript:setPerms( this );" value="1" [{if $idx == 1}]checked[{/if}]>
          </td>
          <td>
            <input [{$readonly}] type="radio" name="aFields[dyn_about]" onclick="JavaScript:setPerms( this );" value="0" [{if !$idx}]checked[{/if}]>
          </td>
          <td>
          </td>
        </tr>

        [{if isset( $aRights.dyn_interface )}]
          [{assign var="idx" value=$aRights.dyn_interface}]
        [{else}]
          [{assign var="idx" value=2}]
        [{/if}]

        [{if isset( $aDynRights.dyn_interface ) && $aDynRights.dyn_interface < $idx}]
          [{assign var="idx" value=$aDynRights.dyn_menu}]
        [{elseif $iParentIdx < $idx}]
          [{assign var="idx" value=$iParentIdx}]
        [{/if}]

        <tr id="dyn_interface">
          <td>
          </td>
          <td class="title">
            [{oxmultilang ident='dyn_interface' noerror=true}]
          </td>
          <td>
            <input [{$readonly}] type="radio" [{if $iParentIdx < 2}]disabled[{/if}] name="aFields[dyn_interface]" onclick="JavaScript:setPerms( this );" value="2" [{if $idx == 2}]checked[{/if}]>
          </td>
          <td>
            <input [{$readonly}] type="radio" [{if $iParentIdx < 1}]disabled[{/if}] name="aFields[dyn_interface]" onclick="JavaScript:setPerms( this );" value="1" [{if $idx == 1}]checked[{/if}]>
          </td>
          <td>
            <input [{$readonly}] type="radio" name="aFields[dyn_interface]" onclick="JavaScript:setPerms( this );" value="0" [{if !$idx}]checked[{/if}]>
          </td>
          <td>
          </td>
        </tr>
        </table>

        <a href="#" onclick="JavaScript:openNode( this );return false;"> &raquo; </a>
      </td>
      <td class="title">
        [{oxmultilang ident='dyn_menu' noerror=true}]
      </td>
      <td>
        <input [{$readonly}] type="radio" [{if isset( $aDynRights.dyn_menu ) && $aDynRights.dyn_menu < 2}]disabled[{/if}] name="aFields[dyn_menu]" onclick="JavaScript:setPerms( this );" value="2" [{if $iParentIdx == 2}]checked[{/if}]>
      </td>
      <td>
        <input [{$readonly}] type="radio" [{if isset( $aDynRights.dyn_menu ) && $aDynRights.dyn_menu < 1}]disabled[{/if}] name="aFields[dyn_menu]" onclick="JavaScript:setPerms( this );" value="1" [{if $iParentIdx == 1}]checked[{/if}]>
      </td>
      <td>
        <input [{$readonly}] type="radio" name="aFields[dyn_menu]" onclick="JavaScript:setPerms( this );" value="0" [{if !$iParentIdx}]checked[{/if}]>
      </td>
      <td>
        <input readonly disabled type="checkbox" id="aFields[dyn_menu]_cust" value="0">
        <script type="text/javascript">
        <!--
            updateCustInfo(document.getElementById("aFields[dyn_menu]_cust"));
        //-->
        </script>
      </td>
    </tr>
  [{/if}]
  [{* service area *}]

  [{* efire area *}]

  [{if $oView->isEfire()}]
      [{if isset( $aDynRights.dyn_efire ) && $aDynRights.dyn_efire || !isset( $aDynRights.dyn_efire )}]
        [{if isset( $aRights.dyn_efire )}]
          [{assign var="iParentIdx" value=$aRights.dyn_efire}]
        [{else}]
          [{assign var="iParentIdx" value=2}]
        [{/if}]
        [{if isset( $aDynRights.dyn_efire ) && $aDynRights.dyn_efire < $iParentIdx}]
          [{assign var="iParentIdx" value=$aDynRights.dyn_efire}]
        [{/if}]

        <tr id="dyn_efire">
          <td>
          </td>
          <td class="title">
            [{oxmultilang ident='dyn_efire' noerror=true}]
          </td>
          <td>
            <input [{$readonly}] type="radio" [{if isset( $aDynRights.dyn_efire ) && $aDynRights.dyn_efire < 2}]disabled[{/if}] name="aFields[dyn_efire]" value="2" [{if $iParentIdx == 2}]checked[{/if}]>
          </td>
          <td>
            <input [{$readonly}] type="radio" [{if isset( $aDynRights.dyn_efire ) && $aDynRights.dyn_efire < 1}]disabled[{/if}] name="aFields[dyn_efire]" value="1" [{if $iParentIdx == 1}]checked[{/if}]>
          </td>
          <td>
            <input [{$readonly}] type="radio" name="aFields[dyn_efire]" value="0" [{if !$iParentIdx}]checked[{/if}]>
          </td>
          <td>
          </td>
        </tr>
      [{/if}]
  [{/if}]
  [{* efire area *}]
  <!-- FCPAYONE BEGIN -->
  [{* PAYONE area *}]
      [{if isset( $aDynRights.fcpo_admin_title ) && $aDynRights.fcpo_admin_title || !isset( $aDynRights.fcpo_admin_title )}]
        [{if isset( $aRights.fcpo_admin_title )}]
          [{assign var="iParentIdx" value=$aRights.fcpo_admin_title}]
        [{else}]
          [{assign var="iParentIdx" value=2}]
        [{/if}]
        [{if isset( $aDynRights.fcpo_admin_title ) && $aDynRights.fcpo_admin_title < $iParentIdx}]
          [{assign var="iParentIdx" value=$aDynRights.fcpo_admin_title}]
        [{/if}]

        <tr id="fcpo_admin_title">
          <td>
          </td>
          <td class="title">
            [{oxmultilang ident='fcpo_admin_title' noerror=true}]
          </td>
          <td>
            <input [{$readonly}] type="radio" [{if isset( $aDynRights.fcpo_admin_title ) && $aDynRights.fcpo_admin_title < 2}]disabled[{/if}] name="aFields[fcpo_admin_title]" value="2" [{if $iParentIdx == 2}]checked[{/if}]>
          </td>
          <td>
            <input [{$readonly}] type="radio" [{if isset( $aDynRights.fcpo_admin_title ) && $aDynRights.fcpo_admin_title < 1}]disabled[{/if}] name="aFields[fcpo_admin_title]" value="1" [{if $iParentIdx == 1}]checked[{/if}]>
          </td>
          <td>
            <input [{$readonly}] type="radio" name="aFields[fcpo_admin_title]" value="0" [{if !$iParentIdx}]checked[{/if}]>
          </td>
          <td>
          </td>
        </tr>
      [{/if}]
  [{* PAYONE area *}]
  <!-- FCPAYONE END -->
</table>

</div>

    </td>
  </tr>

  <tr>
    <td colspan="2">
      <br><i>[{oxmultilang ident="ROLES_BEMAIN_UIINFO"}]</i><br><br>
    </td>
  </tr>