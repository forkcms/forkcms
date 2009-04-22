{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}

<h2>{$msgHeaderIndex}</h2>

<div id="languages-filter">
	{form:filter}
		<dl>
			<dt><label for="language">{$lblLanguage|ucfirst}</label></dt>
				<dd>{$ddmLanguage}</dd>
			<dt><label for="applicatioin">{$lblApplication|ucfirst}</label></dt>
				<dd>{$ddmApplication}</dd>
			<dt><label for="type">{$lblType|ucfirst}</label></dt>
				<dd>{$ddmType}</dd>
			<dt><label for="module">{$lblModule|ucfirst}</label></dt>
				<dd>{$ddmModule}</dd>
			<dt><label for="name">{$lblName|ucfirst}</label></dt>
				<dd>{$txtName}</dd>
			<dt><label for="value">{$lblValue|ucfirst}</label></dt>
				<dd>{$txtValue}</dd>
			<dt>&nbsp;</dt>
				<dd><input type="submit" name="submit" value="{$lblFilter|ucfirst}" /></dd>
		</dl>
	{/form:filter}
</div>

{option:datagrid}{$datagrid}{/option:datagrid}
{option:!datagrid}<p>Allo kroket, der zijn precies geen items die aan uw search criteria voldoen ?</p>{/option:!datagrid}


{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}



<table cellpadding="2" cellspacing="2" border="1">
	<tr>
		<th>application</th>
		<th>module</th>
		<th>type</th>
		<th>naam</th>
		<th>Nederlands</th>
		<th>Frans</th>
		<th>Engels</th>
	</tr>
	<tr>
		<td>backend</td>
		<td>core</td>
		<td>lbl</td>
		<td>Name</td>
		<td>naam</td>
		<td>nom</td>
		<td>name</td>
	</tr>
</table>