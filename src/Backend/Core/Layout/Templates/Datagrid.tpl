<table{option:summary} summary="{$summary}"{/option:summary}{$attributes}>
{option:caption}
<caption>{$caption}</caption>
{/option:caption}
<thead>
  <tr>
    {iteration:headers}
    <th
      {$headers.attributes}>
      {option:headers.sorting}
      {option:headers.sorted}
      {option:headers.sortedAsc}
      <a href="{$headers.sortingURL}" title="{$headers.sortingLabel}" class="sortable sorted">
        {$headers.label} <span class="fa fa-sort-asc"></span>
      </a>
      {/option:headers.sortedAsc}
      {option:headers.sortedDesc}
      <a href="{$headers.sortingURL}" title="{$headers.sortingLabel}" class="sortable sorted">
        {$headers.label} <span class="fa fa-sort-desc"></span>
      </a>
      {/option:headers.sortedDesc}
      {/option:headers.sorted}
      {option:headers.notSorted}
      <a href="{$headers.sortingURL}" title="{$headers.sortingLabel}" class="sortable">
        {$headers.label} <span class="fa fa-sort"></span>
      </a>
      {/option:headers.notSorted}
      {/option:headers.sorting}
      {option:headers.noSorting}
      {option:headers.label}
      <span>{$headers.label}</span>
      {/option:headers.label}
      {option:!headers.label}
      <span>&#160;</span>
      {/option:!headers.label}
      {/option:headers.noSorting}
    </th>
    {/iteration:headers}
  </tr>
</thead>
<tbody>
  {iteration:rows}
  <tr
    {$rows.attributes}>
    {iteration:rows.columns}
    <td
      {$rows.columns.attributes}>{$rows.columns.value}
    </td>
    {/iteration:rows.columns}
  </tr>
  {/iteration:rows}
</tbody>
{option:footer}
<tfoot>
  <tr
    {$footerAttributes}>
    <td colspan="{$numColumns}">
      <div class="form-inline">
        {option:massAction}
        <div class="form-group jsMassAction">
          {$massAction}
        </div>
        {/option:massAction}
      </div>
      {option:paging}
      <div class="text-center">
        {$paging}
      </div>
      {/option:paging}
    </td>
  </tr>
</tfoot>
{/option:footer}
</table>
{option:excludedCheckboxesData}
<script type="text/javascript">
  //<![CDATA[
  window.onload = function()
  {
    if (typeof excludedCheckboxesData != undefined) var excludedCheckboxesData = [];
    excludedCheckboxesData['{$excludedCheckboxesData.id}'] = {$excludedCheckboxesData.JSON
  }
    ;

    // loop and remove elements
    for (var i in
      excludedCheckboxesData['{$excludedCheckboxesData.id}']) {
      $('#{$excludedCheckboxesData.id} input[value=' + excludedCheckboxesData['{$excludedCheckboxesData.id}'][i] + ']').remove();
    }
  }
  //]]>
</script>
{/option:excludedCheckboxesData}
{option:checkedCheckboxesData}
<script type="text/javascript">
  //<![CDATA[
  window.onload = function()
  {
    if (typeof checkedCheckboxesData != undefined) var checkedCheckboxesData = [];
    checkedCheckboxesData['{$checkedCheckboxesData.id}'] = {$checkedCheckboxesData.JSON
  }
    ;

    // loop and remove elements
    for (var i in
      checkedCheckboxesData['{$checkedCheckboxesData.id}']) {
      $('#{$checkedCheckboxesData.id} input[value=' + checkedCheckboxesData['{$checkedCheckboxesData.id}'][i] + ']').prop('checked', true);
    }
  }
  //]]>
</script>
{/option:checkedCheckboxesData}
