<div id="fieldHolder-{$id}" class="form-group jsField">
	<div class="row">
		{option:plaintext}
		<div class="col-md-8">
			<p>{$content}</p>
		</div>
		{/option:plaintext}
		{option:simple}
		<div class="col-md-3">
			<label for="field{$id}">
				{$label}
				{option:required}
				<abbr class="glyphicon glyphicon-asterisk" title="{$lblRequiredField|ucfirst}"></abbr>
				{/option:required}
			</label>
		</div>
		<div class="col-md-5">
			{$field}
		</div>
		{/option:simple}
		{option:multiple}
		<div class="col-md-3">
			<label for="field{$id}">
				{$label}
				{option:required}
				<abbr class="glyphicon glyphicon-asterisk" title="{$lblRequiredField|ucfirst}"></abbr>
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
		<div class="col-md-4">
			<div class="btn-toolbar">
				<div class="btn-group pull-right" role="group">
					<a class="btn btn-danger jsDeleteField" href="#delete-{$id}" rel="{$id}" title="{$lblDelete}">
						<span class="glyphicon glyphicon-trash"></span>
					</a>
					<a class="btn btn-default jsEditField" href="#edit-{$id}" rel="{$id}" title="{$lblEdit}">
						<span class="glyphicon glyphicon-pencil"></span>
					</a>
				</div>
				<div class="btn-group pull-right" role="group">
					<span class="dragAndDropHandle"></span>
				</div>
			</div>
		</div>
	</div>
</div>
