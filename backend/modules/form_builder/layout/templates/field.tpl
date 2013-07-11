<div id="fieldHolder-{$id}" class="field options">
	{* Headings, paragraph *}
	{option:plaintext}
		<div class="fieldWrapper content">
			{$content}
		</div>
	{/option:plaintext}

	{* Text box, textarea, dropdown *}
	{option:simple}
		<div class="fieldWrapper horizontal">
			<label for="field{$id}">
				{$label}{option:required}<abbr title="{$lblRequiredField}">*</abbr>{/option:required}
			</label>
			{$field}
		</div>
	{/option:simple}

	{* Radio button, checkbox *}
	{option:multiple}
		<div class="fieldWrapper horizontal">
			<p class="label">{$label}{option:required}<abbr title="{$lblRequiredField}">*</abbr>{/option:required}</p>
			<ul class="inputList">
			{iteration:items}
				<li><label for="{$items.id}">{$items.field} {$items.label}</label></li>
			{/iteration:items}
			</ul>
		</div>
	{/option:multiple}

	<p class="buttonHolderRight">
		<span class="dragAndDropHandle"></span>
		<a class="button icon iconOnly iconDelete deleteField" href="#delete-{$id}" rel="{$id}"><span>{$lblDelete}</span></a>
		<a class="button icon iconOnly iconEdit editField" href="#edit-{$id}" rel="{$id}"><span>{$lblEdit}</span></a>
	</p>
</div>