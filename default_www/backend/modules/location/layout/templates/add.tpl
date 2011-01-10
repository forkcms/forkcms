{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl'}

{form:add}
	<div class="box">
		<div class="heading">
			<h3>{$lblLocation|ucfirst}: {$lblAdd}</h3>
		</div>
		<div class="content">
			<fieldset>
				<p>
					<label for="title">{$lblTitle|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtTitle} {$txtTitleError}
				</p>
				<p>
					<label for="text">{$lblContent|ucfirst}</label>
					{$txtText} {$txtTextError}
				</p>
			</fieldset>
		</div>
		<div class="content">
			<fieldset>
				<p>
					<label for="street">{$lblStreet|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtStreet} {$txtStreetError}
				</p>
				<p>
					<label for="number">{$lblNumber|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtNumber} {$txtNumberError}
				</p>
				<p>
					<label for="zip">{$lblZip|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtZip} {$txtZipError}
				</p>
				<p>
					<label for="city">{$lblCity|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtCity} {$txtCityError}
				</p>
				<p>
					<label for="country">{$lblCountry|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$ddmCountry} {$ddmCountryError}
				</p>
			</fieldset>
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblPublish|ucfirst}" />
		</div>
	</div>
{/form:add}

{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}