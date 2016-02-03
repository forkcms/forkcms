{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblEditForm|sprintf:{$name}|ucfirst}</h2>
  </div>
</div>
{form:edit}
  <script type="text/javascript">
    //@todo why data comes not from action?
    //<![CDATA[
      var defaultErrorMessages = {};
      {option:errors}
      {iteration:errors}
      defaultErrorMessages.{$errors.type} = '{$errors.message}';
      {/iteration:errors}
      {/option:errors}
    //]]>
  </script>
  <input type="hidden" name="id" id="formId" value="{$id}" />
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div role="tabpanel">
        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active">
            <a href="#tabGeneral" aria-controls="general" role="tab" data-toggle="tab">{$lblGeneral|ucfirst}</a>
          </li>
          <li role="presentation">
            <a href="#tabFields" aria-controls="fields" role="tab" data-toggle="tab">{$lblFields|ucfirst}</a>
          </li>
          <li role="presentation">
            <a href="#tabExtra" aria-controls="extra" role="tab" data-toggle="tab">{$lblExtra|ucfirst}</a>
          </li>
        </ul>
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="tabGeneral">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group{option:txtNameError} has-error{/option:txtNameError}">
                  <label for="name" class="control-label">
                    {$lblName|ucfirst}
                    <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                  </label>
                  {$txtName} {$txtNameError}
                </div>
                <div class="form-group{option:ddmMethodError} has-error{/option:ddmMethodError}">
                  <label for="method" class="control-label">
                    {$lblMethod|ucfirst}
                    <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                  </label>
                  {$ddmMethod} {$ddmMethodError}
                </div>
                <div class="form-group{option:txtEmailError} has-error{/option:txtEmailError}">
                  <label for="email" class="control-label">
                    {$lblRecipient|ucfirst}
                    <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                  </label>
                  {$txtEmail} {$txtEmailError}
                </div>
                <div class="form-group{option:txtSuccessMessageError} has-error{/option:txtSuccessMessageError}">
                  <label for="successMessage" class="control-label">
                    {$lblSuccessMessage|ucfirst}
                    <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                  </label>
                  {$txtSuccessMessage} {$txtSuccessMessageError}
                </div>
              </div>
            </div>
          </div>
          <div role="tabpanel" class="tab-pane" id="tabFields">
            <div class="row">
              <div class="col-md-12">
                <p class="text-warning"><span class="fa fa-warning"></span> {$msgImportantImmediateUpdate}</p>
              </div>
            </div>
            <div class="row">
              <div class="col-md-8">
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h4 class="panel-title">
                      {$lblPreview|ucfirst}
                    </h4>
                  </div>
                  <div id="fieldsHolder" class="panel-body sequenceByDragAndDrop">
                    {option:fields}
                    {iteration:fields}
                    {$fields.field}
                    {/iteration:fields}
                    {/option:fields}

                    {* This row always needs to be here. We show/hide it with javascript *}
                    <div id="noFields" class="alert alert-info"{option:fields} style="display: none;"{/option:fields}>
                      {$msgNoFields}
                    </div>

                    {* Submit button is always here. Cannot be deleted or moved. *}
                  <div class="row">
                    <div class="col-md-5 col-md-offset-4">
                      <div class="btn-toolbar">
                        <div class="btn-group pull-right" role="group">
                          {$btnSubmitField}
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                        <div class="btn-group pull-right" role="group">
                          <a href="#edit-{$submitId}" class="btn btn-default jsFieldEdit" rel="{$submitId}" title="{$lblEdit}">
                            <span class="fa fa-pencil"></span>
                          </a>
                        </div>
                    </div>
                  </div>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div id="formElements" class="panel panel-default">
                  <div class="panel-heading">
                    <h4 class="panel-title">
                      {$lblAddFields|ucfirst}
                    </h4>
                  </div>
                  <div class="panel-body">
                    <h5>{$lblFormElements|ucfirst}</h5>
                    <ul class="list-group">
                      <li id="textboxSelector" class="list-group-item">
                        <a href="#textbox" rel="textboxDialog" class="jsFieldDialogTrigger">
                          <span class="fa fa-square-o fa-fw"></span>
                          {$lblTextbox|ucfirst}
                        </a>
                      </li>
                      <li id="textareaSelector" class="list-group-item">
                        <a href="#textarea" rel="textareaDialog" class="jsFieldDialogTrigger">
                          <span class="fa fa-square-o fa-fw"></span>
                          {$lblTextarea|ucfirst}
                        </a>
                      </li>
                      <li id="datetimeSelector" class="list-group-item">
                        <a href="#datetime" rel="datetimeDialog" class="jsFieldDialogTrigger">
                          <span class="fa fa-calendar-o fa-fw"></span> {$lblDatetime|ucfirst}
                        </a>
                      </li>
                      <li id="dropdownSelector" class="list-group-item">
                        <a href="#dropdown" rel="dropdownDialog" class="jsFieldDialogTrigger">
                          <span class="fa fa-list-alt fa-fw"></span> {$lblDropdown|ucfirst}
                        </a>
                      </li>
                      <li id="checkboxSelector" class="list-group-item">
                        <a href="#checkbox" rel="checkboxDialog" class="jsFieldDialogTrigger">
                          <span class="fa fa-check-square-o fa-fw"></span> {$lblCheckbox|ucfirst}
                        </a>
                      </li>
                      <li id="radiobuttonSelector" class="list-group-item">
                        <a href="#radiobutton" rel="radiobuttonDialog" class="jsFieldDialogTrigger">
                          <span class="fa fa-dot-circle-o fa-fw"></span> {$lblRadiobutton|ucfirst}
                        </a>
                      </li>
                    </ul>
                    <h5>{$lblTextElements|ucfirst}</h5>
                    <ul class="list-group">
                      <li id="headingSelector" class="list-group-item">
                        <a href="#heading" rel="headingDialog" class="jsFieldDialogTrigger">
                          <span class="fa fa-header fa-fw"></span> {$lblHeading|ucfirst}
                        </a>
                      </li>
                      <li id="paragraphSelector" class="list-group-item">
                        <a href="#paragraph" rel="paragraphDialog" class="jsFieldDialogTrigger">
                          <span class="fa fa-align-left fa-fw"></span> {$lblParagraph|ucfirst}
                        </a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div role="tabpanel" class="tab-pane" id="tabExtra">
            <div class="row">
              <div class="col-md-12">
                <label for="identifier" class="control-label">
                  {$lblIdentifier|ucfirst}
                  <abbr class="fa fa-info-circle" data-toggle="tooltip" title="{$msgHelpIdentifier}"></abbr>
                </label>
                {$txtIdentifier} {$txtIdentifierError}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row fork-page-actions">
    <div class="col-md-12">
      <div class="btn-toolbar">
        <div class="btn-group pull-left" role="group">
          {option:showFormBuilderDelete}
          <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirmDelete">
            <span class="fa fa-trash-o"></span>
            {$lblDelete|ucfirst}
          </button>
          {/option:showFormBuilderDelete}
        </div>
        <div class="btn-group pull-right" role="group">
          <button id="editButton" type="submit" name="edit" class="btn btn-success">
            <span class="fa fa-floppy-o"></span>&nbsp;
            {$lblSave|ucfirst}
          </button>
        </div>
      </div>
    </div>
  </div>
  {include:{$BACKEND_MODULES_PATH}/FormBuilder/Layout/Templates/Dialogs.tpl}
{/form:edit}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
