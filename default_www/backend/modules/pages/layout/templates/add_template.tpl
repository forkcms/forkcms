{include:file='{$BACKEND_CORE_PATH}/layout/templates/header.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl'}
		<td id="contentHolder">
			<div class="inner">
				{form:add}
					<div class="box horizontal">
						<div class="heading">
							<h3>{$lblAddTemplate|ucfirst}</h3>
						</div>
						<div class="options">
							<p>
								<label for="file">{$lblFileName|ucfirst}</label>
								<small><code>core/layout/templates/</code></small>{$txtFile} {$txtFileError}
							</p>
							<p>
								<label for="label">{$lblLabel|ucfirst}</label>
								{$txtLabel} {$txtLabelError}
							</p>
							<p>
								<label for="numBlocks">{$lblNumberOfBlocks|ucfirst}</label>
								{$ddmNumBlocks} {$ddmNumBlocksError}
							</p>
						</div>
						{* Don't change this ID *}
						<div id="metaData" class="options">
							{iteration:names}
							<p>
								<label for="name{$names.i}">{$lblName|ucfirst} {$names.i}</label>
								{$names.txtName} {$names.ddmType}
								{$names.txtNameError} {$names.ddmTypeError}
							</p>
							{/iteration:names}
						</div>
						<div class="options">
							<p>
								<label for="format">{$lblLayout|ucfirst}</label>
								{$txtFormat} {$txtFormatError}
								{* @todo add label *}
								<span class="helpTxt">e.g. [0,1],[2,none]</span>
							</p>
						</div>
						<div class="options">
							<div class="spacing">
								<ul class="inputList pb0">
									<li>{$chkActive} <label for="active">{$lblActive|ucfirst}</label> {$chkActiveError}</li>
									<li>{$chkDefault} <label for="default">{$msgIsDefault}</label> {$chkDefaultError}</li>
								</ul>
							</div>
						</div>
					</div>

					<div class="fullwidthOptions">
						<div class="buttonHolderRight">
							<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblAddTemplate|ucfirst}" />
						</div>
					</div>
				{/form:add}
			</div>
		</td>
	</tr>
</table>
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}