[{oxscript include="js/widgets/oxinputvalidator.js" priority=10 }]
[{oxscript add="$('form.js-oxValidate').oxInputValidator();"}]

<form class="js-oxValidate" action="[{$oViewConf->getSelfActionLink()}]" name="changepassword" method="post">
    [{assign var="aErrors" value=$oView->getFieldValidationErrors()}]
        [{$oViewConf->getHiddenSid()}]
        [{$oViewConf->getNavFormParams()}]
        <input type="hidden" name="fnc" value="changePassword">
        <input type="hidden" name="cl" value="account_password">
        <input type="hidden" name="CustomError" value='user'>
        <input type="hidden" id="passwordLength" value="[{$oViewConf->getPasswordLength()}]">
    <ul class="form clear">
        <li [{if $aErrors.oxuser__oxpassword}]class="invalid-field"[{/if}]>
            <input type="password" id="passwordOld" name="password_old" class="js-oxValidate js-oxValidate_notEmpty textbox" placeholder="[{oxmultilang ident="OLD_PASSWORD"}]" />
            <p class="validation-error">
                <span class="js-oxError_notEmpty">[{oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS"}]</span>
                [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxuser__oxpassword}]
            </p>
        </li>
        <li [{if $aErrors.oxuser__oxpassword}]class="invalid-field"[{/if}]>
            <input type="password" id="passwordNew" name="password_new" class="js-oxValidate js-oxValidate_notEmpty js-oxValidate_length js-oxValidate_match textbox" placeholder="[{oxmultilang ident="NEW_PASSWORD"}]" />
            <p class="validation-error">
                <span class="js-oxError_notEmpty">[{oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS"}]</span>
                <span class="js-oxError_length">[{oxmultilang ident="ERROR_MESSAGE_PASSWORD_TOO_SHORT"}]</span>
                <span class="js-oxError_match">[{oxmultilang ident="ERROR_MESSAGE_USER_PWDDONTMATCH"}]</span>
                [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxuser__oxpassword}]
            </p>
        </li>
        <li [{if $aErrors.oxuser__oxpassword}]class="invalid-field"[{/if}]>
            <input type="password" id="passwordNewConfirm" name="password_new_confirm" class="js-oxValidate js-oxValidate_notEmpty js-oxValidate_length js-oxValidate_match textbox" placeholder="[{oxmultilang ident="CONFIRM_PASSWORD"}]" />
            <p class="validation-error">
                <span class="js-oxError_notEmpty">[{oxmultilang ident="ERROR_MESSAGE_INPUT_NOTALLFIELDS"}]</span>
                <span class="js-oxError_length">[{oxmultilang ident="ERROR_MESSAGE_PASSWORD_TOO_SHORT"}]</span>
                <span class="js-oxError_match">[{oxmultilang ident="ERROR_MESSAGE_USER_PWDDONTMATCH"}]</span>
                [{include file="message/inputvalidation.tpl" aErrors=$aErrors.oxuser__oxpassword}]
            </p>
        </li>
        <li>
            <button id="savePass" type="submit" class="btn">[{oxmultilang ident="SAVE"}]</button>
        </li>
    </ul>
</form>
