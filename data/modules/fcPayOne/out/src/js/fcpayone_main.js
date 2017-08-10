/* 
 * JS of fcpayone_main.js
 */

function handlePresaveOrderCheckbox(oCheckbox) {
    if(oCheckbox.checked) {
        document.getElementById('reduce_stock').style.display = "";
    } else {
        document.getElementById('reduce_stock').style.display = "none";
    }
}

function handleRatePayShowDetails(oCheckbox) {
    var sOxid = oCheckbox.value;
    var sId = 'ratepay_profile_details_' + sOxid;
    if(oCheckbox.checked) {
        document.getElementById(sId).style.display = 'block';
    } else {
        document.getElementById(sId).style.display = 'none';
    }
}

function _groupExp(oElement) {
    var _cur = oElement.parentNode;

    if (_cur.className === "exp") {
        _cur.className = "";
    } else {
        _cur.className = "exp";
    }
}

function toggleHostedTemplate() {
    if(document.getElementById('fcpoHostedCCTemplate').style.display !== "") {
        document.getElementById('fcpoHostedCCTemplate').style.display = "";
    } else {
        document.getElementById('fcpoHostedCCTemplate').style.display = "none";
    }
}

function togglePreview() {
    if(document.getElementById('fcpoHostedCCPreview').style.display !== "") {
        document.getElementById('fcpoHostedCCPreview').style.display = "";
    } else {
        document.getElementById('fcpoHostedCCPreview').style.display = "none";
    }
}

function inputEnable(sInputId) {
    document.getElementById(sInputId).disabled = false;
}
function inputDisable(sInputId) {
    document.getElementById(sInputId).disabled = true;
}

function handleSizeFields(oSelect, sRowName) {
    if(oSelect.options[oSelect.selectedIndex].value === 'custom') {
        inputEnable('input_height_' + sRowName);
        inputEnable('input_width_' + sRowName);
    } else {
        inputDisable('input_height_' + sRowName);
        inputDisable('input_width_' + sRowName);            
    }
}

function handleCss(oSelect, sRowName) {
    if(oSelect.options[oSelect.selectedIndex].value === 'custom') {
        inputEnable('input_css_' + sRowName);
    } else {
        inputDisable('input_css_' + sRowName);
    }
}