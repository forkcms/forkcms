{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>{$lblModuleSettings|ucfirst}: {$lblProfiles}</h2>
</div>

{form:settings}

    <div class="box">
        <div class="heading">
            <h3>{$lblNotifications|ucfirst}</h3>
        </div>
        <div class="options">
            <ul class="inputList">
                <li><label for="sendNewProfileAdminMail">{$chkSendNewProfileAdminMail} {$lblSendNewProfileAdminMail|ucfirst}</label></li>
                <li id="overwriteProfileNotificationEmailBox"><label for="overwriteProfileNotificationEmail">{$chkOverwriteProfileNotificationEmail} {$lblOverwriteProfileNotificationEmail|ucfirst}</label><br/>
                    <span id="profileNotificationEmailBox">
                        {$txtProfileNotificationEmail}<abbr title="{$lblRequiredField|ucfirst}">*</abbr> {$txtProfileNotificationEmailError}
                    </span>
                </li>
            </ul>
        </div>
        <div class="options">
            <ul class="inputList">
                <li><label for="sendNewProfileMail">{$chkSendNewProfileMail} {$lblSendNewProfileMail|ucfirst}</label></li>
            </ul>
        </div>
    </div>

    <div class="fullwidthOptions">
        <div class="buttonHolderRight">
            <input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
        </div>
    </div>
{/form:settings}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
