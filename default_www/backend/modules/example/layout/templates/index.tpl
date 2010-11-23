{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl'}

{form:test}
<div class="box">
	<div class="heading">
		<h3>Your section title</h3>
	</div>
	<div class="options">
		<p>
			<label for="something">Something<abbr title="Required field">*</abbr></label>
			<input type="text" class="inputText" />
		</p>
	</div>
	<div class="options optionsRTE">
		<p>
			<label for="something">Something<abbr title="Required field">*</abbr></label>
			{$txtEditor}
		</p>
	</div>
</div>
{/form:test}

{form:test}
<div class="box">
	<div class="heading">
		<h3>Your section title</h3>
	</div>
	<div class="options horizontal">
		<p>
			<label for="something">Something<abbr title="Required field">*</abbr></label>
			<input type="text" class="inputText" />
		</p>
	</div>
	<div class="options horizontal">
		<p>
			<label for="something">Something<abbr title="Required field">*</abbr></label>
			{$txtEditor2}
		</p>
	</div>
</div>
{/form:test}

{form:test}
<div class="box">
	<div class="heading">
		<h3>Your section title</h3>
	</div>
	<div class="options optionsRTE">
		{$txtEditor3}
	</div>
</div>
{/form:test}

{form:test}
<div class="box">
	<div class="heading">
		<h3>Your section title</h3>
	</div>
	<div class="optionsRTE">
		{$txtEditor4}
	</div>
</div>
{/form:test}

{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}