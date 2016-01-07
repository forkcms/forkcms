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
          <div class="form-group">
            <label for="overviewNumItems">{$lblItemsPerPage|ucfirst}</label>
            {$ddmOverviewNumItems} {$ddmOverviewNumItemsError}
          </div>
          <div class="form-group">
            <label for="autocompleteNumItems">{$lblItemsForAutocomplete|ucfirst}</label>
            {$ddmAutocompleteNumItems} {$ddmAutocompleteNumItemsError}
          </div>
          <div class="form-group">
            <label for="autosuggestNumItems">{$lblItemsForAutosuggest|ucfirst}</label>
            {$ddmAutosuggestNumItems} {$ddmAutosuggestNumItemsError}
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
                <label for="useSitelinksSearchBox">
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
          <p class="text-info">{$msgHelpWeightGeneral}</p>
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
              <td><label for="{$modules.id}">{$modules.label}</label></td>
              <td>
                <label for="{$modules.id}Weight" class="visuallyHidden">{$lblWeight|ucfirst}</label>
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
          <button id="save" type="submit" name="save" class="btn btn-primary">{$lblSave|ucfirst}</button>
        </div>
      </div>
    </div>
  </div>
{/form:settings}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
