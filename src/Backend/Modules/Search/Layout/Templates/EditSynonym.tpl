{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblEditSynonym|ucfirst}</h2>
  </div>
</div>
{form:editItem}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="form-group{option:txtTermError} has-error{/option:txtTermError}">
        <label for="term">
          {$lblTerm|ucfirst}
          <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
        </label>
        {$txtTerm} {$txtTermError}
      </div>
      <div class="form-group{option:txtSynonymError} has-error{/option:txtSynonymError}">
        <div class="fakeP">
          <label for="addValue-synonym">
            {$lblSynonyms|ucfirst}
            <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
          </label>
          <div class="itemAdder">
            {$txtSynonym} {$txtSynonymError}
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row fork-page-actions">
    <div class="col-md-12">
      <div class="btn-toolbar">
        <div class="btn-group pull-left" role="group">
          {option:showSearchDeleteSynonym}
          <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirmDelete">
            <span class="fa fa-trash-o"></span>
            {$lblDelete|ucfirst}
          </button>
          {/option:showSearchDeleteSynonym}
        </div>
        <div class="btn-group pull-right" role="group">
          <button id="addButton" type="submit" name="add" class="btn btn-primary">
            <span class="fa fa-plus"></span>&nbsp;
            {$lblAddSynonym|ucfirst}
          </button>
        </div>
      </div>
      {option:showSearchDeleteSynonym}
      <div class="modal fade" id="confirmDelete" tabindex="-1" role="dialog" aria-labelledby="{$lblDelete|ucfirst}" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <span class="modal-title h4">{$lblDelete|ucfirst}</span>
            </div>
            <div class="modal-body">
              <p>{$msgConfirmDeleteSynonym|sprintf:{$term}}</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
              <a href="{$var|geturl:'delete_synonym'}&amp;id={$record.id}" class="btn btn-primary">
                {$lblOK|ucfirst}
              </a>
            </div>
          </div>
        </div>
      </div>
      {/option:showSearchDeleteSynonym}
    </div>
  </div>
{/form:editItem}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
