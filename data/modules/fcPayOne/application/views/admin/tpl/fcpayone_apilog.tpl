[{include file="headitem.tpl" title="SYSREQ_MAIN_TITLE"|oxmultilangassign}]

[{ if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form autocomplete="off" name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="fcpayone_apilog">
</form>

[{ if $oxid == '-1' }]

[{oxmultilang ident="FCPO_NO_APILOG"}]

[{ else }]

    <table style="width: 100%;">
        <tr>
            <td style="vertical-align: top;width: 50%;">
                REQUEST:<br>
                [{if $edit->getRequestArray() != false }]
                    <table style="border: 1px solid #C8C8C8;">
                        [{assign var="blWhite" value=""}]
                        [{foreach from=$edit->getRequestArray() key=key item=entry}]
                            [{assign var="listclass" value=listitem$blWhite }]
                            <tr>
                                <td class="[{ $listclass}]">[{$key}]</td>
                                <td class="[{ $listclass}]">[{$entry}]</td>
                            </tr>
                            [{if $blWhite == "2"}]
                                [{assign var="blWhite" value=""}]
                            [{else}]
                                [{assign var="blWhite" value="2"}]
                            [{/if}]
                        [{/foreach}]
                     </table>
                [{else}]
                    <pre>[{ $edit->fcporequestlog__fcpo_request->value }]</pre>
                [{/if}]
            </td>
            <td style="vertical-align: top;">
				RESPONSE:<br>
                [{if $edit->getResponseArray() != false }]
                    <table style="border: 1px solid #C8C8C8;">
                        [{assign var="blWhite" value=""}]
                        [{foreach from=$edit->getResponseArray() key=key item=entry}]
                            [{assign var="listclass" value=listitem$blWhite }]
                            <tr>
                                <td class="[{ $listclass}]">[{$key}]</td>
                                <td class="[{ $listclass}]">[{$entry}]</td>
                            </tr>
                            [{if $blWhite == "2"}]
                                [{assign var="blWhite" value=""}]
                            [{else}]
                                [{assign var="blWhite" value="2"}]
                            [{/if}]
                        [{/foreach}]
                    </table>
                [{else}]
                    <pre>[{ $edit->fcporequestlog__fcpo_response->value }]</pre>
                [{/if}]
            </td>
        </tr>
    </table>

[{/if}]

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]