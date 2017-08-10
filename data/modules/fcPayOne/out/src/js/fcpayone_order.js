/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function editThisStatus( sID, sOxid ) {
    var oTransfer = top.basefrm.edit.document.getElementById( "transfer" );
    oTransfer.status_oxid.value = sID;
    oTransfer.oxid.value = sOxid;
    oTransfer.cl.value = top.oxid.admin.getClass( sID );

    //forcing edit frame to reload after submit
    top.forceReloadingEditFrame();

    var oSearch = top.basefrm.list.document.getElementById( "search" );
    oSearch.oxid.value = sOxid;
    oSearch.submit();
}

function toggleBankaccount() {
     if(top.basefrm.edit.document.getElementById('fcBankAccount1').style.display == 'none') {
         top.basefrm.edit.document.getElementById('fcBankAccount1').style.display = '';
         top.basefrm.edit.document.getElementById('fcBankAccount2').style.display = '';
         top.basefrm.edit.document.getElementById('fcBankAccount3').style.display = '';
         top.basefrm.edit.document.getElementById('fcBankAccount4').style.display = '';
         top.basefrm.edit.document.getElementById('fcShowBankaccount').style.display = 'none';
         top.basefrm.edit.document.getElementById('fcHideBankaccount').style.display = '';
     } else {
         top.basefrm.edit.document.getElementById('fcBankAccount1').style.display = 'none';
         top.basefrm.edit.document.getElementById('fcBankAccount2').style.display = 'none';
         top.basefrm.edit.document.getElementById('fcBankAccount3').style.display = 'none';
         top.basefrm.edit.document.getElementById('fcBankAccount4').style.display = 'none';
         top.basefrm.edit.document.getElementById('fcHideBankaccount').style.display = 'none';
         top.basefrm.edit.document.getElementById('fcShowBankaccount').style.display = '';
     }
}

function onClickCapture(oElement) {
    var dCaptureAmount          = parseFloat(document.getElementById('fc_capture_amount').value.replace(',', '.'));;
    var sErrorMessageCapture    = document.getElementById('fc_error_message_capture_greater_null').value;
    var sConfirmSure            = document.getElementById('fc_confirm_message').value;
    
    if(dCaptureAmount == 0) {
        alert(sErrorMessageCapture);
    } else {
        if(confirm(sConfirmSure)) {
            oElement.form.fnc.value='capture';
            oElement.form.submit();
        }
    }
}


