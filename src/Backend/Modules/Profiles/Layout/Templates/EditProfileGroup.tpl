{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblEditGroup|ucfirst}</h2>
  </div>
</div>
{form:editProfileGroup}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="form-group{option:ddmGroupError} has-error{/option:ddmGroupError}">
        <label for="group">{$lblGroup|ucfirst}</label>
        {$ddmGroup} {$ddmGroupError}
      </div>
      <div class="form-group">
        <label for="expirationDate">{$lblExpiresOn|ucfirst}:</label>
        <div class="form-inline">
          <div class="form-group{option:txtExpirationDateError} has-error{/option:txtExpirationDateError}">
            {$txtExpirationDate} {$txtExpirationDateError}
          </div>
          <div class="form-group{option:txtExpirationTimeError} has-error{/option:txtExpirationTimeError}">
            {$txtExpirationTime} {$txtExpirationTimeError}
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row fork-page-actions">
    <div class="col-md-12">
      <div class="btn-toolbar">
        <div class="btn-group pull-left" role="group">
          {option:showProfilesDeleteProfileGroup}
          <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirmDelete">
            <span class="fa fa-trash-o"></span>
            {$lblDelete|ucfirst}
          </button>
          {/option:showProfilesDeleteProfileGroup}
        </div>
        <div class="btn-group pull-right" role="group">
          <button id="saveButton" type="submit" name="edit" class="btn btn-primary">
            <span class="fa fa-floppy-o"></span>&nbsp;{$lblSave|ucfirst}
          </button>
        </div>
      </div>
      {option:showProfilesDeleteProfileGroup}
      <div class="modal fade" id="confirmDelete" tabindex="-1" role="dialog" aria-labelledby="{$lblDelete|ucfirst}" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <span class="modal-title h4">{$lblDelete|ucfirst}</span>
            </div>
            <div class="modal-body">
              <p>{$msgConfirmProfileGroupDelete|sprintf:{$profileGroup.name}}</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
              <a href="{$var|geturl:'delete_profile_group'}&amp;id={$profileGroup.id}" class="btn btn-primary">
                {$lblOK|ucfirst}
              </a>
            </div>
          </div>
        </div>
      </div>
      {/option:showProfilesDeleteProfileGroup}
    </div>
  </div>
{/form:editProfileGroup}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
