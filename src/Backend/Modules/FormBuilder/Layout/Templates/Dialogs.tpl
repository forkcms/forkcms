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
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="tabTextboxBasic">
                        <div class="row">
                            <div class="col-md-12">
                                <h3>{$lblBasic|ucfirst}</h3>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="textboxLabel">
                                        {$lblLabel|ucfirst}
                                        <abbr class="glyphicon glyphicon-asterisk" title="{$lblRequiredField|ucfirst}"></abbr>
                                    </label>
                                    {$txtTextboxLabel}
                                    <p id="textboxLabelError" class="text-danger" style="display: none;"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tabTextboxProperties">
                        <div class="row">
                            <div class="col-md-12">
                                <h3>{$lblProperties|ucfirst}</h3>
                            </div>
                        </div>
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
                                                <abbr class="glyphicon glyphicon-info-sign" title="{$msgHelpReplyTo}"></abbr>
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
                                            <abbr class="glyphicon glyphicon-asterisk" title="{$lblRequiredField|ucfirst}"></abbr>
                                        </label>
                                        {$txtTextboxRequiredErrorMessage}
                                        <p id="textboxRequiredErrorMessageError" class="text-danger" style="display: none;"></p>
                                    </div>
                                </div>
                                <div class="jsValidation" style="display: none;">
                                    <div class="form-group">
                                        <label for="textboxValidation">{$lblValidation|ucfirst}</label>
                                        {$ddmTextboxValidation}
                                    </div>
                                    <div class="form-group jsValidationParameter" style="display: none;">
                                        <label for="textboxValidationParameter">
                                            {$lblParameter|ucfirst}
                                            <abbr class="glyphicon glyphicon-asterisk" title="{$lblRequiredField|ucfirst}"></abbr>
                                        </label>
                                        {$txtTextboxValidationParameter}
                                    </div>
                                    <div class="form-group jsValidationErrorMessage" style="display: none;">
                                        <label for="textareaErrorMessage">
                                            {$lblErrorMessage|ucfirst}
                                            <abbr class="glyphicon glyphicon-asterisk" title="{$lblRequiredField|ucfirst}"></abbr>
                                        </label>
                                        {$txtTextboxErrorMessage}
                                        <p id="textboxErrorMessageError" class="text-danger" style="display: none;"></p>
                                    </div>
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
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#tabTextareaBasic" aria-controls="basic" role="tab" data-toggle="tab">{$lblBasic|ucfirst}</a>
                    </li>
                    <li role="presentation">
                        <a href="#tabTextareaProperties" aria-controls="properties" role="tab" data-toggle="tab">{$lblProperties|ucfirst}</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="tabTextareaBasic">
                        <div class="row">
                            <div class="col-md-12">
                                <h3>{$lblBasic|ucfirst}</h3>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="textareaLabel">
                                        {$lblLabel|ucfirst}
                                        <abbr class="glyphicon glyphicon-asterisk" title="{$lblRequiredField|ucfirst}"></abbr>
                                    </label>
                                    {$txtTextareaLabel}
                                    <p id="textareaLabelError" class="text-danger" style="display: none;"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tabTextareaProperties">
                        <div class="row">
                            <div class="col-md-12">
                                <h3>{$lblProperties|ucfirst}</h3>
                            </div>
                        </div>
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
                                            <abbr class="glyphicon glyphicon-asterisk" title="{$lblRequiredField|ucfirst}"></abbr>
                                        </label>
                                        {$txtTextareaRequiredErrorMessage}
                                        <p id="textareaRequiredErrorMessageError" class="text-danger" style="display: none;"></p>
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
                                            <abbr class="glyphicon glyphicon-asterisk" title="{$lblRequiredField|ucfirst}"></abbr>
                                        </label>
                                        {$txtTextareaValidationParameter}
                                    </div>
                                    <div class="form-group jsValidationErrorMessage" style="display: none;">
                                        <label for="textareaErrorMessage">
                                            {$lblErrorMessage|ucfirst}
                                            <abbr class="glyphicon glyphicon-asterisk" title="{$lblRequiredField|ucfirst}"></abbr>
                                        </label>
                                        {$txtTextareaErrorMessage}
                                        <p id="textareaErrorMessageError" class="text-danger" style="display: none;"></p>
                                    </div>
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
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#tabDropdownBasic" aria-controls="basic" role="tab" data-toggle="tab">{$lblBasic|ucfirst}</a>
                    </li>
                    <li role="presentation">
                        <a href="#tabDropdownProperties" aria-controls="properties" role="tab" data-toggle="tab">{$lblProperties|ucfirst}</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="tabDropdownBasic">
                        <div class="row">
                            <div class="col-md-12">
                                <h3>{$lblBasic|ucfirst}</h3>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="dropdownLabel">
                                        {$lblLabel|ucfirst}
                                        <abbr class="glyphicon glyphicon-asterisk" title="{$lblRequiredField|ucfirst}"></abbr>
                                    </label>
                                    {$txtDropdownLabel}
                                    <p id="dropdownLabelError" class="text-danger" style="display: none;"></p>
                                </div>
                                <div class="form-group">
                                    <label for="checkboxValues">{$lblValues|ucfirst}</label>
                                    {$txtDropdownValues} {$txtDropdownValuesError}
                                    <p id="dropdownValuesError" class="text-danger" style="display: none;"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tabDropdownProperties">
                        <div class="row">
                            <div class="col-md-12">
                                <h3>{$lblProperties|ucfirst}</h3>
                            </div>
                        </div>
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
                                            <abbr class="glyphicon glyphicon-asterisk" title="{$lblRequiredField|ucfirst}"></abbr>
                                        </label>
                                        {$txtDropdownRequiredErrorMessage}
                                        <p id="dropdownRequiredErrorMessageError" class="text-danger" style="display: none;"></p>
                                    </div>
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
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#tabRadiobuttonBasic" aria-controls="basic" role="tab" data-toggle="tab">{$lblBasic|ucfirst}</a>
                    </li>
                    <li role="presentation">
                        <a href="#tabRadiobuttonProperties" aria-controls="properties" role="tab" data-toggle="tab">{$lblProperties|ucfirst}</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="tabRadiobuttonBasic">
                        <div class="row">
                            <div class="col-md-12">
                                <h3>{$lblBasic|ucfirst}</h3>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="radiobuttonLabel">
                                        {$lblLabel|ucfirst}
                                        <abbr class="glyphicon glyphicon-asterisk" title="{$lblRequiredField|ucfirst}"></abbr>
                                    </label>
                                    {$txtRadiobuttonLabel}
                                    <p id="radiobuttonLabelError" class="text-danger" style="display: none;"></p>
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
                                <h3>{$lblProperties|ucfirst}</h3>
                            </div>
                        </div>
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
                                            <abbr class="glyphicon glyphicon-asterisk" title="{$lblRequiredField|ucfirst}"></abbr>
                                        </label>
                                        {$txtRadiobuttonRequiredErrorMessage}
                                        <p id="radiobuttonRequiredErrorMessageError" class="text-danger" style="display: none;"></p>
                                    </div>
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
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#tabCheckboxBasic" aria-controls="basic" role="tab" data-toggle="tab">{$lblBasic|ucfirst}</a>
                    </li>
                    <li role="presentation">
                        <a href="#tabCheckboxProperties" aria-controls="properties" role="tab" data-toggle="tab">{$lblProperties|ucfirst}</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="tabCheckboxBasic">
                        <div class="row">
                            <div class="col-md-12">
                                <h3>{$lblBasic|ucfirst}</h3>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="checkboxLabel">
                                        {$lblLabel|ucfirst}
                                        <abbr class="glyphicon glyphicon-asterisk" title="{$lblRequiredField|ucfirst}"></abbr>
                                    </label>
                                    {$txtCheckboxLabel}
                                    <p id="checkboxLabelError" class="text-danger" style="display: none;"></p>
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
                                <h3>{$lblProperties|ucfirst}</h3>
                            </div>
                        </div>
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
                                            <abbr class="glyphicon glyphicon-asterisk" title="{$lblRequiredField|ucfirst}"></abbr>
                                        </label>
                                        {$txtCheckboxRequiredErrorMessage}
                                        <p id="checkboxRequiredErrorMessageError" class="text-danger" style="display: none;"></p>
                                    </div>
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
                <div class="form-group">
                    <label for="submit">
                        {$lblContent|ucfirst}
                        <abbr class="glyphicon glyphicon-asterisk" title="{$lblRequiredField|ucfirst}"></abbr>
                    </label>
                    {$txtSubmit}
                    <div class="jsValidation">
                        <p id="submitError" class="text-danger" style="display: none;"></p>
                    </div>
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
                        <abbr class="glyphicon glyphicon-asterisk" title="{$lblRequiredField|ucfirst}"></abbr>
                    </label>
                    {$txtHeading}
                    <p id="headingError" class="text-danger" style="display: none;"></p>
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
                    {$txtParagraph}
                    <p id="paragraphError" class="text-danger" style="display: none;"></p>
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
