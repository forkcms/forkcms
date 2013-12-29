{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

{form:addItem}
	<div class="box">
		<div class="heading">
			<h3>{$lblSearch|ucfirst}: {$lblAddSynonym}</h3>
		</div>
		<div class="options horizontal">
			<p>
				<label for="term">{$lblTerm|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtTerm} {$txtTermError}
			</p>
			<div class="fakeP">
				<label for="addValue-synonym">{$lblSynonyms|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				<div class="itemAdder">
					{$txtSynonym} {$txtSynonymError}
				</div>
			</div>
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblAddSynonym|ucfirst}" />
		</div>
	</div>
{/form:addItem}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
