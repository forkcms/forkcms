<table{option:summary} summary="{$summary}"{/option:summary}{$attributes}>
	{option:caption}<caption>{$caption}</caption>{/option:caption}
	<thead>
		<tr{$headerAttributes}>
			{iteration:headers}
				<th{option:headers.sorting} class="sortable{option:headers.sorted} sorted{/option:headers.sorted}{option:headers.sortedAsc} sortedAsc{/option:headers.sortedAsc}{option:headers.sortedDesc} sortedDesc{/option:headers.sortedDesc}"{/option:headers.sorting}>
					{option:headers.sorting}
						{option:headers.sorted}
							<a href="{$headers.sortingURL}" title="{$headers.sortingLabel}">{$headers.label}</a>
						{/option:headers.sorted}
						{option:headers.notSorted}
							<a href="{$headers.sortingURL}" title="{$headers.sortingLabel}">{$headers.label}</a>
						{/option:headers.notSorted}
					{/option:headers.sorting}
					
					{option:headers.noSorting}
						{$headers.label}
					{/option:headers.noSorting}
				</th>
			{/iteration:headers}
		</tr>
	</thead>
	<tbody>
		{iteration:rows}
			<tr{$rows.attributes}{$rows.oddAttributes}{$rows.evenAttributes}>
				{iteration:rows.columns}<td{$columns.attributes}>{$columns.value}</td>{/iteration:rows.columns}
			</tr>
		{/iteration:rows}
	</tbody>
	{option:paging}
		<tfoot>
			<tr{$footerAttributes}>
				<td colspan="{$numColumns}">{$paging}</td>
			</tr>
		</tfoot>
	{/option:paging}
</table>