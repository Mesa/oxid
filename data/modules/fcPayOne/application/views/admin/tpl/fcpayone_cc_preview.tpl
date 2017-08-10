[{ oxmultilang ident="FCPO_PREVIEW_NOTICE" }]
<div style="border:1px solid #000;padding:10px;background-color: #fff;">
    <script type="text/javascript" src="https://secure.pay1.de/client-api/js/v1/payone_hosted_min.js"></script>
    <input type="hidden" name="dynvalue[fcpo_kknumber]" value="">
    <input type="hidden" name="fcpo_cc_type" value="hosted">
    <table>
        <tr>
            <td><label for="cardpanInput">[{ oxmultilang ident="FCPO_NUMBER" }]</label></td>
            <td><span class="inputIframe" id="cardpan"></span></td>
        </tr>
        <tr>
            <td><label for="cvcInput">[{ oxmultilang ident="FCPO_CARD_SECURITY_CODE" }]</label></td>
            <td><span id="cardcvc2" class="inputIframe"></span></td>
        </tr>
        <tr>
            <td><label for="expireInput">[{ oxmultilang ident="FCPO_VALID_UNTIL" }]</label></td>
            <td>
                <span id="expireInput" class="inputIframe">
                    <span id="cardexpiremonth"></span>
                    <span id="cardexpireyear"></span>
                </span>
            </td>
        </tr>
    </table>
    [{oxscript add=$oView->fcpoGetJsCardPreviewCode()}]            
</div>