{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblSettings|ucfirst}</h2>
  </div>
</div>
{form:settings}
  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            {$lblPagination|ucfirst}
          </h3>
        </div>
        <div class="panel-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group{option:ddmOverviewNumItemsError} has-error{/option:ddmOverviewNumItemsError}">
                <label for="overviewNumItems" class="control-label">{$lblItemsPerPage|ucfirst}</label>
                <br>
                <br>
                {$ddmOverviewNumItems} {$ddmOverviewNumItemsError}
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group{option:ddmAutocompleteNumItemsError} has-error{/option:ddmAutocompleteNumItemsError}">
                <label for="autocompleteNumItems" class="control-label">{$lblItemsForAutocomplete|ucfirst}</label>
                {$ddmAutocompleteNumItems} {$ddmAutocompleteNumItemsError}
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group{option:ddmAutosuggestNumItemsError} has-error{/option:ddmAutosuggestNumItemsError}">
                <label for="autosuggestNumItems" class="control-label">{$lblItemsForAutosuggest|ucfirst}</label>
                {$ddmAutosuggestNumItems} {$ddmAutosuggestNumItemsError}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            {$lblSitelinksSearchBox|ucfirst}
          </h3>
        </div>
        <div class="panel-body">
          <div class="form-group">
            <ul class="list-unstyled">
              <li class="checkbox">
                <label for="useSitelinksSearchBox" class="control-label">
                  {$chkUseSitelinksSearchBoxError} {$chkUseSitelinksSearchBox|ucfirst} {$lblUseSitelinksSearchBox|ucfirst}
                  <abbr class="fa fa-question-circle" data-toggle="tooltip" title="{$msgHelpSitelinksSearchBox}"></abbr>
                </label>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            {$lblModuleWeight|ucfirst}
          </h3>
        </div>
        <div id="searchModules" class="panel-body">
          <p class="help-block">{$msgHelpWeightGeneral}</p>
          <table class="table table-hover table-striped jsDataGrid">
            <tr>
              <th>{$msgIncludeInSearch}</th>
              <th>{$lblModule|ucfirst}</th>
              <th>
                {$lblWeight|ucfirst}
                <abbr class="fa fa-question-circle" data-toggle="tooltip" title="{$msgHelpWeight}"></abbr>
              </th>
            </tr>
            {iteration:modules}
            <tr class="{cycle:odd:even}">
              <td>{$modules.chk}</td>
              <td><label for="{$modules.id}" class="control-label">{$modules.label}</label></td>
              <td>
                <label for="{$modules.id}Weight" class="visuallyHidden" class="control-label">{$lblWeight|ucfirst}</label>
                {$modules.txt}
                {option:modules.txtError}
                <p class="text-danger">{$modules.txtError}</p>
                {/option:modules.txtError}
              </td>
            </tr>
            {/iteration:modules}
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="btn-toolbar">
        <div class="btn-group pull-right" role="group">
          <button id="save" type="submit" name="save" class="btn btn-success"><span class="fa fa-floppy-o"></span> {$lblSave|ucfirst}</button>
        </div>
      </div>
    </div>
  </div>
{/form:settings}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
