<table{option:oSummary} summary="{$summary}"{/option:oSummary}{$attributes}>
	{option:oCaption}<caption>{$caption}</caption>{/option:oCaption}
	<thead>
		<tr{$header.attributes}>
			{iteration:iHeader}
			<th>
			{option:oSorting}
				{option:oSorted}<a href="{$sorting.url}" title="{$sorting.label}">{$label} <img src="{$sorting.icon}" /></a>{/option:oSorted}
				{option:oNotSorted}<a href="{$sorting.url}" title="{$sorting.label}">{$label} <img src="{$sorting.icon}" /></a>{/option:oNotSorted}
			{/option:oSorting}
			{option:oNoSorting}
				{$label}
			{/option:oNoSorting}
			</th>
			{/iteration:iHeader}
		</tr>
	</thead>
	{option:oPaging}
	<tfoot>
		<tr>
			<td>
				{$paging}
			</td>
		</tr>
	</tfoot>
	{/option:oPaging}
	<tbody>
		{iteration:iRows}
		<tr{$row.attributes}{$row.oddAttributes}{$row.evenAttributes}>
			{iteration:iColumns}
				<td{$column.attributes}>{$column.value}</td>
			{/iteration:iColumns}
		</tr>
		{/iteration:iRows}
	</tbody>
</table>