<table{option:summary} summary="{$summary}"{/option:summary}{$attributes}>
	{option:caption}<caption>{$caption}</caption>{/option:caption}
	<thead>
		<tr{$headerAttributes}>
			{iteration:headers}
			<th>
			{option:sorting}
				{option:sorted}<a href="{$sorting.url}" title="{$sorting.label}">{$label} <img src="{$sorting.icon}" /></a>{/option:sorted}
				{option:notSorted}<a href="{$sorting.url}" title="{$sorting.label}">{$label} <img src="{$sorting.icon}" /></a>{/option:notSorted}
			{/option:sorting}
			{option:noSorting}
				{$label}
			{/option:noSorting}
			</th>
			{/iteration:headers}
		</tr>
	</thead>
	<tbody>
		{iteration:rows}
		<tr{$attributes}{$oddAttributes}{$evenAttributes}>
			{iteration:columns}<td{$attributes}>{$value}</td>{/iteration:columns}
		</tr>
		{/iteration:rows}
	</tbody>
	{option:paging}
	<tfoot>
		<tr{$footerAttributes}>
			<td>{$paging}</td>
		</tr>
	</tfoot>
	{/option:paging}
</table>