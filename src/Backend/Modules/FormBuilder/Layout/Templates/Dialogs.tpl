{* Dialog for a textbox *}
<div class="modal fade jsFieldDialog" id="textboxDialog" tabindex="-1" role="dialog" aria-labelledby="{$lblTextbox|ucfirst}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title h4">{$lblTextbox|ucfirst}</span>
            </div>
            <div class="modal-body">
                <input type="hidden" name="textbox_id" id="textboxId" value="" />
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#tabTextboxBasic" aria-controls="basic" role="tab" data-toggle="tab">{$lblBasic|ucfirst}</a>
                    </li>
                    <li role="presentation">
                        <a href="#tabTextboxProperties" aria-controls="properties" role="tab" data-toggle="tab">{$lblProperties|ucfirst}</a>
                    </li>
                    <li role="presentation">
                      <a href="#tabTextboxAdvanced" aria-controls="advanced" role="tab" data-toggle="tab">{$lblAdvanced|ucfirst}</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane jsFieldTab active" id="tabTextboxBasic">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="textboxLabel">
                                        {$lblLabel|ucfirst}
                                        <abbr data-toggle="tooltop" title="{$lblRequiredField|ucfirst}">*</abbr>
                                    </label>
                                    <p id="textboxLabelError" class="text-danger jsFieldError" style="display: none;"></p>
                                    {$txtTextboxLabel}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tabTextboxProperties">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="textboxValue">{$lblDefaultValue|ucfirst}</label>
                                    {$txtTextboxValue}
                                </div>
                                <div class="form-group">
                                    <ul class="list-unstyled">
                                        <li class="checkbox">
                                            <label for="textboxReplyTo">
                                                {$chkTextboxReplyTo} {$lblReplyTo|ucfirst}
                                                <abbr class="fa fa-info-circle" data-toggle="tooltip" title="{$msgHelpReplyTo}"></abbr>
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                                <div class="jsValidation">
                                    <ul class="list-unstyled">
                                        <li class="checkbox">
                                            <label for="textboxRequired">
                                                {$chkTextboxRequired} {$lblRequiredField|ucfirst}
                                            </label>
                                        </li>
                                    </ul>
                                    <div class="form-group jsValidationRequiredErrorMessage" style="display: none;">
                                        <label for="textboxRequiredErrorMessage">
                                            {$lblErrorMessage|ucfirst}
                                            <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                                        </label>
                                        <p id="textboxRequiredErrorMessageError" class="text-danger jsFieldError" style="display: none;"></p>
                                        {$txtTextboxRequiredErrorMessage}
                                    </div>
                                </div>
                                <div class="jsValidation">
                                    <div class="form-group">
                                        <label for="textboxValidation">{$lblValidation|ucfirst}</label>
                                        {$ddmTextboxValidation}
                                    </div>
                                    <p id="textboxReplyToErrorMessageError" class="text-danger" style="display: none;"></p>
                                    <div class="form-group jsValidationParameter" style="display: none;">
                                        <label for="textboxValidationParameter">
                                            {$lblParameter|ucfirst}
                                            <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                                        </label>
                                        {$txtTextboxValidationParameter}
                                    </div>
                                    <div class="form-group jsValidationErrorMessage" style="display: none;">
                                        <label for="textareaErrorMessage">
                                            {$lblErrorMessage|ucfirst}
                                            <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                                        </label>
                                        <p id="textboxErrorMessageError" class="text-danger jsFieldError" style="display: none;"></p>
                                        {$txtTextboxErrorMessage}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane jsFieldTab" id="tabTextboxAdvanced">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="textboxPlaceholder">{$lblPlaceholder|ucfirst}</label>
                                    {$txtTextboxPlaceholder}
                                </div>
                                <div class="form-group">
                                    <label for="textboxClassname">{$lblClassname|ucfirst}</label>
                                    {$txtTextboxClassname}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
                <button id="textboxDialogSubmit" type="button" class="btn btn-primary jsFieldDialogSubmit">{$lblOK|ucfirst}</button>
            </div>
        </div>
    </div>
</div>

{* Dialog for a textarea *}
<div class="modal fade jsFieldDialog" id="textareaDialog" tabindex="-1" role="dialog" aria-labelledby="{$lblTextarea|ucfirst}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title h4">{$lblTextarea|ucfirst}</span>
            </div>
            <div class="modal-body">
                <input type="hidden" name="textarea_id" id="textareaId" value="" />
                <ul class="nav nav-tabs jsFieldTabsNav" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#tabTextareaBasic" aria-controls="basic" role="tab" data-toggle="tab">{$lblBasic|ucfirst}</a>
                    </li>
                    <li role="presentation">
                        <a href="#tabTextareaProperties" aria-controls="properties" role="tab" data-toggle="tab">{$lblProperties|ucfirst}</a>
                    </li>
                    <li role="presentation">
                        <a href="#tabTextareaAdvanced" aria-controls="advanced" role="tab" data-toggle="tab">{$lblAdvanced|ucfirst}</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane jsFieldTab active" id="tabTextareaBasic">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="textareaLabel">
                                        {$lblLabel|ucfirst}
                                        <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                                    </label>
                                    <p id="textareaLabelError" class="text-danger jsFieldError" style="display: none;"></p>
                                    {$txtTextareaLabel}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tabTextareaProperties">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="textareaValue">{$lblDefaultValue|ucfirst}</label>
                                    {$txtTextareaValue}
                                </div>
                                <div class="jsValidation">
                                    <div class="form-group">
                                        <ul class="list-unstyled">
                                            <li class="checkbox">
                                                <label for="textareaRequired">{$chkTextareaRequired} {$lblRequiredField|ucfirst}</label>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="form-group jsValidationRequiredErrorMessage" style="display: none;">
                                        <label for="textareaRequiredErrorMessage">
                                            {$lblErrorMessage|ucfirst}
                                            <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                                        </label>
                                        <p id="textareaRequiredErrorMessageError" class="text-danger jsFieldError" style="display: none;"></p>
                                        {$txtTextareaRequiredErrorMessage}
                                    </div>
                                </div>
                                <div class="jsValidation" style="display: none;">
                                    <div class="form-group">
                                        <label for="textareaValidation">{$lblValidation|ucfirst}</label>
                                        {$ddmTextareaValidation}
                                    </div>
                                    <div class="form-group jsValidationParameter" style="display: none;">
                                        <label for="textareaValidationParameter">
                                            {$lblParameter|ucfirst}
                                            <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                                        </label>
                                        {$txtTextareaValidationParameter}
                                    </div>
                                    <div class="form-group jsValidationErrorMessage" style="display: none;">
                                        <label for="textareaErrorMessage">
                                            {$lblErrorMessage|ucfirst}
                                            <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                                        </label>
                                        <p id="textareaErrorMessageError" class="text-danger jsFieldError" style="display: none;"></p>
                                        {$txtTextareaErrorMessage}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane jsFieldTab" id="tabTextareaAdvanced">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="textareaPlaceholder">{$lblPlaceholder|ucfirst}</label>
                                    {$txtTextareaPlaceholder}
                                </div>
                                <div class="form-group">
                                    <label for="textareaClassname">{$lblClassname|ucfirst}</label>
                                    {$txtTextareaClassname}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
                <button id="textareaDialogSubmit" type="button" class="btn btn-primary jsFieldDialogSubmit">{$lblOK|ucfirst}</button>
            </div>
        </div>
    </div>
</div>

{* Dialog for a datetime *}
<div class="modal fade jsFieldDialog" id="datetimeDialog" tabindex="-1" role="dialog" aria-labelledby="{$lblDatetime|ucfirst}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title h4">{$lblTextarea|ucfirst}</span>
            </div>
            <div class="modal-body">
                <input type="hidden" name="datetime_id" id="datetimeId" value="" />
                <ul class="nav nav-tabs jsFieldTabsNav" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#tabDatetimeBasic" aria-controls="basic" role="tab" data-toggle="tab">{$lblBasic|ucfirst}</a>
                    </li>
                    <li role="presentation">
                        <a href="#tabDatetimeProperties" aria-controls="properties" role="tab" data-toggle="tab">{$lblProperties|ucfirst}</a>
                    </li>
                    <li role="presentation">
                        <a href="#tabDatetimeAdvanced" aria-controls="advanced" role="tab" data-toggle="tab">{$lblAdvanced|ucfirst}</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane jsFieldTab active" id="tabDatetimeBasic">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="datetimeLabel">
                                        {$lblLabel|ucfirst}
                                        <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                                    </label>
                                    <p id="datetimeLabelError" class="text-danger jsFieldError" style="display: none;"></p>
                                    {$txtDatetimeLabel}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tabDatetimeProperties">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group jsDefaultValue">
                                    <label for="datetimeValue">{$lblDefaultValue|ucfirst}</label>
                                    <div class="form-inline">
                                        {$ddmDatetimeValueAmount} {$ddmDatetimeValueType}
                                    </div>
                                </div>
                                <div class="jsValidation">
                                    <div class="form-group">
                                        <ul class="list-unstyled">
                                            <li class="checkbox">
                                                <label for="datetimeRequired">{$chkDatetimeRequired} {$lblRequiredField|ucfirst}</label>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="form-group jsValidationRequiredErrorMessage" style="display: none;">
                                        <label for="datetimeRequiredErrorMessage">
                                            {$lblErrorMessage|ucfirst}
                                            <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                                        </label>
                                        <p id="datetimeRequiredErrorMessageError" class="text-danger jsFieldError" style="display: none;"></p>
                                        {$txtDatetimeRequiredErrorMessage}
                                    </div>
                                </div>
                                <div>
                                    <div class="form-group">
                                        <label for="datetimeType">{$lblType|ucfirst}</label>
                                        {$ddmDatetimeType}
                                    </div>
                                </div>
                                <div class="jsValidation" style="display: none;">
                                    <div class="form-group">
                                        <label for="datetimeValidation">{$lblValidation|ucfirst}</label>
                                        {$ddmDatetimeValidation}
                                    </div>
                                    <div class="form-group jsValidationErrorMessage" style="display: none;">
                                        <label for="datetimeErrorMessage">
                                            {$lblErrorMessage|ucfirst}
                                            <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                                        </label>
                                        <p id="datetimeErrorMessageError" class="text-danger jsFieldError" style="display: none;"></p>
                                        {$txtDatetimeErrorMessage}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane jsFieldTab" id="tabDatetimeAdvanced">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="datetimeClassname">{$lblClassname|ucfirst}</label>
                                    {$txtDatetimeClassname}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
                <button id="textareaDialogSubmit" type="button" class="btn btn-primary jsFieldDialogSubmit">{$lblOK|ucfirst}</button>
            </div>
        </div>
    </div>
</div>

{* Dialog for a dropdown *}
<div class="modal fade jsFieldDialog" id="dropdownDialog" tabindex="-1" role="dialog" aria-labelledby="{$lblDropdown|ucfirst}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title h4">{$lblDropdown|ucfirst}</span>
            </div>
            <div class="modal-body">
                <input type="hidden" name="dropdown_id" id="dropdownId" value="" />
                <ul class="nav nav-tabs jsFieldTabsNav" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#tabDropdownBasic" aria-controls="basic" role="tab" data-toggle="tab">{$lblBasic|ucfirst}</a>
                    </li>
                    <li role="presentation">
                        <a href="#tabDropdownProperties" aria-controls="properties" role="tab" data-toggle="tab">{$lblProperties|ucfirst}</a>
                    </li>
                    <li role="presentation">
                        <a href="#tabDropdownAdvanced" aria-controls="advanced" role="tab" data-toggle="tab">{$lblAdvanced|ucfirst}</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane jsFieldTab active" id="tabDropdownBasic">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="dropdownLabel">
                                        {$lblLabel|ucfirst}
                                        <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                                    </label>
                                    <p id="dropdownLabelError" class="text-danger jsFieldError" style="display: none;"></p>
                                    {$txtDropdownLabel}
                                </div>
                                <div class="form-group">
                                    <label for="checkboxValues">{$lblValues|ucfirst}</label>
                                    <p id="dropdownValuesError" class="text-danger jsFieldError" style="display: none;"></p>
                                    {$txtDropdownValues} {$txtDropdownValuesError}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tabDropdownProperties">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="dropdownDefaultValue">{$lblDefaultValue|ucfirst}</label>
                                    {$ddmDropdownDefaultValue}
                                </div>
                                <div class="jsValidation">
                                    <div class="form-group">
                                        <ul class="list-unstyled">
                                            <li class="checkbox">
                                                <label for="dropdownRequired">{$chkDropdownRequired} {$lblRequiredField|ucfirst}</label>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="form-group jsValidationRequiredErrorMessage" style="display: none;">
                                        <label for="dropdownRequiredErrorMessage">
                                            {$lblErrorMessage|ucfirst}
                                            <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                                        </label>
                                        <p id="dropdownRequiredErrorMessageError" class="text-danger jsFieldError" style="display: none;"></p>
                                        {$txtDropdownRequiredErrorMessage}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane jsFieldTab" id="tabDropdownAdvanced">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="dropdownClassname">{$lblClassname|ucfirst}</label>
                                    {$txtDropdownClassname}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
                <button id="dropdownDialogSubmit" type="button" class="btn btn-primary jsFieldDialogSubmit">{$lblOK|ucfirst}</button>
            </div>
        </div>
    </div>
</div>

{* Dialog for a radiobutton *}
<div class="modal fade jsFieldDialog" id="radiobuttonDialog" tabindex="-1" role="dialog" aria-labelledby="{$lblRadiobutton|ucfirst}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title h4">{$lblRadiobutton|ucfirst}</span>
            </div>
            <div class="modal-body">
                <input type="hidden" name="radiobutton_id" id="radiobuttonId" value="" />
                <ul class="nav nav-tabs jsFieldTabsNav" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#tabRadiobuttonBasic" aria-controls="basic" role="tab" data-toggle="tab">{$lblBasic|ucfirst}</a>
                    </li>
                    <li role="presentation">
                        <a href="#tabRadiobuttonProperties" aria-controls="properties" role="tab" data-toggle="tab">{$lblProperties|ucfirst}</a>
                    </li>
                    <li role="presentation">
                        <a href="#tabRadiobuttonAdvanced" aria-controls="advanced" role="tab" data-toggle="tab">{$lblAdvanced|ucfirst}</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane jsFieldTab active" id="tabRadiobuttonBasic">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="radiobuttonLabel">
                                        {$lblLabel|ucfirst}
                                        <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                                    </label>
                                    <p id="radiobuttonLabelError" class="text-danger jsFieldError" style="display: none;"></p>
                                    {$txtRadiobuttonLabel}
                                </div>
                                <div class="form-group">
                                    <label for="checkboxValues">{$lblValues|ucfirst}</label>
                                    {$txtRadiobuttonValues} {$txtRadiobuttonValuesError}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tabRadiobuttonProperties">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="checkboxDefaultValue">{$lblDefaultValue|ucfirst}</label>
                                    {$ddmRadiobuttonDefaultValue}
                                </div>
                                <div class="jsValidation">
                                    <div class="form-group">
                                        <ul class="list-unstyled">
                                            <li class="checkbox">
                                                <label for="checkboxRequired">{$chkRadiobuttonRequired} {$lblRequiredField|ucfirst}</label>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="form-group jsValidationRequiredErrorMessage" style="display: none;">
                                        <label for="checkboxRequiredErrorMessage">
                                            {$lblErrorMessage|ucfirst}
                                            <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                                        </label>
                                        <p id="radiobuttonRequiredErrorMessageError" class="text-danger jsFieldError" style="display: none;"></p>
                                        {$txtRadiobuttonRequiredErrorMessage}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane jsFieldTab" id="tabRadiobuttonAdvanced">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="radiobuttonClassname">{$lblClassname|ucfirst}</label>
                                    {$txtRadiobuttonClassname}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
                <button id="radiobuttonDialogSubmit" type="button" class="btn btn-primary jsFieldDialogSubmit">{$lblOK|ucfirst}</button>
            </div>
        </div>
    </div>
</div>

{* Dialog for a checkbox *}
<div class="modal fade jsFieldDialog" id="checkboxDialog" tabindex="-1" role="dialog" aria-labelledby="{$lblCheckbox|ucfirst}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title h4">{$lblCheckbox|ucfirst}</span>
            </div>
            <div class="modal-body">
                <input type="hidden" name="checkbox_id" id="checkboxId" value="" />
                <ul class="nav nav-tabs jsFieldTabsNav" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#tabCheckboxBasic" aria-controls="basic" role="tab" data-toggle="tab">{$lblBasic|ucfirst}</a>
                    </li>
                    <li role="presentation">
                        <a href="#tabCheckboxProperties" aria-controls="properties" role="tab" data-toggle="tab">{$lblProperties|ucfirst}</a>
                    </li>
                    <li role="presentation">
                        <a href="#tabCheckboxAdvanced" aria-controls="advanced" role="tab" data-toggle="tab">{$lblAdvanced|ucfirst}</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane jsFieldTab active" id="tabCheckboxBasic">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="checkboxLabel">
                                        {$lblLabel|ucfirst}
                                        <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                                    </label>
                                    <p id="checkboxLabelError" class="text-danger jsFieldError" style="display: none;"></p>
                                    {$txtCheckboxLabel}
                                </div>
                                <div class="form-group">
                                    <label for="checkboxValues">{$lblValues|ucfirst}</label>
                                    {$txtCheckboxValues} {$txtCheckboxValuesError}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tabCheckboxProperties">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="checkboxDefaultValue">{$lblDefaultValue|ucfirst}</label>
                                    {$ddmCheckboxDefaultValue}
                                </div>
                                <div class="jsValidation">
                                    <div class="form-group">
                                        <ul class="list-unstyled">
                                            <li class="checkbox">
                                                <label for="checkboxRequired">{$chkCheckboxRequired} {$lblRequiredField|ucfirst}</label>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="form-group jsValidationRequiredErrorMessage" style="display: none;">
                                        <label for="checkboxRequiredErrorMessage">
                                            {$lblErrorMessage|ucfirst}
                                            <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                                        </label>
                                        <p id="checkboxRequiredErrorMessageError" class="text-danger jsFieldError" style="display: none;"></p>
                                        {$txtCheckboxRequiredErrorMessage}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane jsFieldTab" id="tabCheckboxAdvanced">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="checkboxClassname">{$lblClassname|ucfirst}</label>
                                    {$txtCheckboxClassname}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
                <button id="checkboxDialogSubmit" type="button" class="btn btn-primary jsFieldDialogSubmit">{$lblOK|ucfirst}</button>
            </div>
        </div>
    </div>
</div>

{* Dialog for the submit button *}
<div class="modal fade jsFieldDialog" id="submitDialog" tabindex="-1" role="dialog" aria-labelledby="{$lblSubmitButton|ucfirst}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title h4">{$lblSubmitButton|ucfirst}</span>
            </div>
            <div class="modal-body">
                <input type="hidden" name="submit_id" id="submitId" value="" />
                <div class="form-group">
                    <label for="submit">
                        {$lblContent|ucfirst}
                        <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                    </label>
                    <p id="submitError" class="text-danger jsFieldError" style="display: none;"></p>
                    {$txtSubmit}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
                <button id="submitDialogSubmit" type="button" class="btn btn-primary jsFieldDialogSubmit">{$lblOK|ucfirst}</button>
            </div>
        </div>
    </div>
</div>

{* Dialog for a heading *}
<div class="modal fade jsFieldDialog" id="headingDialog" tabindex="-1" role="dialog" aria-labelledby="{$lblHeading|ucfirst}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title h4">{$lblHeading|ucfirst}</span>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="heading">
                        {$lblContent|ucfirst}
                        <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                    </label>
                    <p id="headingError" class="text-danger jsFieldError" style="display: none;"></p>
                    {$txtHeading}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
                <button id="headingDialogSubmit" type="button" class="btn btn-primary jsFieldDialogSubmit">{$lblOK|ucfirst}</button>
            </div>
        </div>
    </div>
</div>

{* Dialog for a paragraph *}
<div class="modal fade jsFieldDialog" id="paragraphDialog" tabindex="-1" role="dialog" aria-labelledby="{$lblContent|ucfirst}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title h4">{$lblContent|ucfirst}</span>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <p id="paragraphError" class="text-danger jsFieldError" style="display: none;"></p>
                    {$txtParagraph}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
                <button id="paragraphDialogSubmit" type="button" class="btn btn-primary jsFieldDialogSubmit">{$lblOK|ucfirst}</button>
            </div>
        </div>
    </div>
</div>

{* Page delete confirm block *}
{option:showFormBuilderDelete}
{option:id}
{option:name}
<div class="modal fade" id="confirmDelete" tabindex="-1" role="dialog" aria-labelledby="{$lblDelete|ucfirst}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title h4">{$lblDelete|ucfirst}</span>
            </div>
            <div class="modal-body">
                <p>{$msgConfirmDelete|sprintf:{$name}}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
                <a href="{$var|geturl:'delete'}&amp;id={$id}" class="btn btn-primary">
                    {$lblOK|ucfirst}
                </a>
            </div>
        </div>
    </div>
</div>
{/option:name}
{/option:id}
{/option:showFormBuilderDelete}
