<div id="fieldHolder-{$id}" class="form-group jsField">
  <div class="row">
    <div class="col-md-1">
      <span class="dragAndDropHandle">
        <span class="fa fa-navicon"></span>
      </span>
    </div>
    {option:plaintext}
    <div class="col-md-8">
      {$content}
    </div>
    {/option:plaintext}
    {option:simple}
    <div class="col-md-3">
      <label for="field{$id}" class="control-label">
        {$label}
        {option:required}
        <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
        {/option:required}
      </label>
    </div>
    <div class="col-md-5">
      {$field}
    </div>
    {/option:simple}
    {option:multiple}
    <div class="col-md-3">
      <label for="field{$id}" class="control-label">
        {$label}
        {option:required}
        <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
        {/option:required}
      </label>
    </div>
    <div class="col-md-5">
      <ul class="list-unstyled">
        {iteration:items}
        <li class="checkbox">
          <label for="{$items.id}">{$items.field} {$items.label}</label>
        </li>
        {/iteration:items}
      </ul>
    </div>
    {/option:multiple}
    <div class="col-md-3">
      <div class="btn-toolbar">
        <div class="btn-group pull-right" role="group">
          <a class="btn btn-default jsFieldDelete" href="#delete-{$id}" rel="{$id}" title="{$lblDelete}">
            <span class="fa fa-trash-o"></span>
          </a>
          <a class="btn btn-default jsFieldEdit" href="#edit-{$id}" rel="{$id}" title="{$lblEdit}">
            <span class="fa fa-pencil"></span>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
