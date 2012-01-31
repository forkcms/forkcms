<table{option:summary} summary="{$summary}"{/option:summary}{$attributes}>
	{option:caption}<caption>{$caption}</caption>{/option:caption}
	<thead>
		<tr>
			{iteration:headers}
				<th{$headers.attributes}>
					{option:headers.sorting}
						{option:headers.sorted}
							<a href="{$headers.sortingURL}" title="{$headers.sortingLabel}" class="sortable sorted{option:headers.sortedAsc} sortedAsc{/option:headers.sortedAsc}{option:headers.sortedDesc} sortedDesc{/option:headers.sortedDesc}">{$headers.label}</a>
						{/option:headers.sorted}
						{option:headers.notSorted}
							<a href="{$headers.sortingURL}" title="{$headers.sortingLabel}" class="sortable">{$headers.label}</a>
						{/option:headers.notSorted}
					{/option:headers.sorting}

					{option:headers.noSorting}
						{option:headers.label}<span>{$headers.label}</span>{/option:headers.label}
						{option:!headers.label}<span>&#160;</span>{/option:!headers.label}
					{/option:headers.noSorting}
				</th>
			{/iteration:headers}
		</tr>
	</thead>
	<tbody>
		{iteration:rows}
			<tr{$rows.attributes}>
				{iteration:rows.columns}<td{$rows.columns.attributes}>{$rows.columns.value}</td>{/iteration:rows.columns}
			</tr>
		{/iteration:rows}
	</tbody>
	{option:footer}
		<tfoot>
			<tr{$footerAttributes}>
				<td colspan="{$numColumns}">
					<div class="tableOptionsHolder">
						<div class="tableOptions">
							{option:massAction}<div class="oneLiner massAction">{$massAction}</div>{/option:massAction}
							{option:paging}<div class="pagination">{$paging}</div>{/option:paging}
						</div>
					</div>
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
			if(typeof excludedCheckboxesData != undefined) var excludedCheckboxesData = new Array();
			excludedCheckboxesData['{$excludedCheckboxesData.id}'] = {$excludedCheckboxesData.JSON};

			// loop and remove elements
			for(var i in excludedCheckboxesData['{$excludedCheckboxesData.id}']) $('#{$excludedCheckboxesData.id} input[value='+ excludedCheckboxesData['{$excludedCheckboxesData.id}'][i] +']').remove();
		}
	//]]>
</script>
{/option:excludedCheckboxesData}

{option:checkedCheckboxesData}
<script type="text/javascript">
	//<![CDATA[
		window.onload = function()
		{
			if(typeof checkedCheckboxesData != undefined) var checkedCheckboxesData = new Array();
			checkedCheckboxesData['{$checkedCheckboxesData.id}'] = {$checkedCheckboxesData.JSON};

			// loop and remove elements
			for(var i in checkedCheckboxesData['{$checkedCheckboxesData.id}']) $('#{$checkedCheckboxesData.id} input[value='+ checkedCheckboxesData['{$checkedCheckboxesData.id}'][i] +']').prop('checked', true);
		}
	//]]>
</script>
{/option:checkedCheckboxesData}