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
                <h3>{$lblGeneral|ucfirst}</h3>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="name">
                    {$lblName|ucfirst}
                    <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                  </label>
                  {$txtName} {$txtNameError}
                </div>
                <div class="form-group">
                  <label for="method">
                    {$lblMethod|ucfirst}
                    <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                  </label>
                  {$ddmMethod} {$ddmMethodError}
                </div>
                <div class="form-group">
                  <label for="email">
                    {$lblRecipient|ucfirst}
                    <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                  </label>
                  {$txtEmail} {$txtEmailError}
                </div>
                <div class="form-group">
                  <label for="successMessage">
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
                <h3>{$lblFields|ucfirst}</h3>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <p class="text-warning">{$msgImportantImmediateUpdate}</p>
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
                    <div class="btn-toolbar">
                      <div class="btn-group pull-left" role="group">
                        {$btnSubmitField}
                      </div>
                      <div class="btn-group pull-right" role="group">
                        <a href="#edit-{$submitId}" class="btn btn-default jsFieldEdit" rel="{$submitId}" title="{$lblEdit}">
                          <span class="fa fa-pencil"></span>
                        </a>
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
                          {$lblTextbox|ucfirst}
                        </a>
                      </li>
                      <li id="textareaSelector" class="list-group-item">
                        <a href="#textarea" rel="textareaDialog" class="jsFieldDialogTrigger">
                          {$lblTextarea|ucfirst}
                        </a>
                      </li>
                      <li id="datetimeSelector" class="list-group-item">
                        <a href="#datetime" rel="datetimeDialog" class="jsFieldDialogTrigger">
                          {$lblDatetime|ucfirst}
                        </a>
                      </li>
                      <li id="dropdownSelector" class="list-group-item">
                        <a href="#dropdown" rel="dropdownDialog" class="jsFieldDialogTrigger">
                          {$lblDropdown|ucfirst}
                        </a>
                      </li>
                      <li id="checkboxSelector" class="list-group-item">
                        <a href="#checkbox" rel="checkboxDialog" class="jsFieldDialogTrigger">
                          {$lblCheckbox|ucfirst}
                        </a>
                      </li>
                      <li id="radiobuttonSelector" class="list-group-item">
                        <a href="#radiobutton" rel="radiobuttonDialog" class="jsFieldDialogTrigger">
                          {$lblRadiobutton|ucfirst}
                        </a>
                      </li>
                    </ul>
                    <h5>{$lblTextElements|ucfirst}</h5>
                    <ul class="list-group">
                      <li id="headingSelector" class="list-group-item">
                        <a href="#heading" rel="headingDialog" class="jsFieldDialogTrigger">
                          {$lblHeading|ucfirst}
                        </a>
                      </li>
                      <li id="paragraphSelector" class="list-group-item">
                        <a href="#paragraph" rel="paragraphDialog" class="jsFieldDialogTrigger">
                          {$lblParagraph|ucfirst}
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
                <h3>{$lblExtra|ucfirst}</h3>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <label for="identifier">
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
          <button id="editButton" type="submit" name="edit" class="btn btn-primary">
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
