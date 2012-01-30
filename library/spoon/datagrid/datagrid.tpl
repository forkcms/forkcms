{*
	Below a list of globally availble options & variables.

	Variables:
	- {$summary} / table summary
	- {$caption} / table caption
	- {$paging} / output of the paging class
	- {$numColumns} / number of visible columns

	Options:
	- paging / whether pagination is enabled
*}

<table{option:summary} summary="{$summary}"{/option:summary}{$attributes}>
	{option:caption}<caption>{$caption}</caption>{/option:caption}
	<thead>
		<tr>
			{iteration:headers}
				<th{$headers.attributes}>
					{option:headers.sorting}
						{option:headers.sorted}
							<a href="{$headers.sortingURL}" title="{$headers.sortingLabel}">{$headers.label}{option:headers.sortingIcon} <img src="{$headers.sortingIcon}" />{/option:headers.sortingIcon}</a>
						{/option:headers.sorted}
						{option:headers.notSorted}
							<a href="{$headers.sortingURL}" title="{$headers.sortingLabel}">{$headers.label}{option:headers.sortingIcon} <img src="{$headers.sortingIcon}" />{/option:headers.sortingIcon}</a>
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
			<tr{$rows.attributes}>
				{iteration:rows.columns}<td{$rows.columns.attributes}>{$rows.columns.value}</td>{/iteration:rows.columns}
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
