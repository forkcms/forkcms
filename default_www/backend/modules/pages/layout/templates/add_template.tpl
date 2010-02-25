{include:file='{$BACKEND_CORE_PATH}/layout/templates/header.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl'}
		<td id="contentHolder">
			<div id="statusBar">
				<p class="breadcrumb">{$lblPages|ucfirst} &gt; {$lblAdd|ucfirst}</p>
			</div>

			<div class="inner">
				{form:add}
					<div class="box horizontal">
						<div class="heading">
							{* @todo add label *}
							<h3>Voeg template toe</h3>
						</div>
						<div class="options">
							<p>
								<label for="path">{$lblPath|ucfirst}</label>
								{$txtPath} {$txtPathError}
							</p>
							<p>
								<label for="label">{$lblLabel|ucfirst}</label>
								{$txtLabel} {$txtLabelError}
							</p>
							<p>
								<label for="num_blocks">{$lblNumberOfBlocks|ucfirst}</label>
								{$ddmNumBlocks} {$ddmNumBlocksError}
							</p>
						</div>
						<div class="options">
							{iteration:names}
							<p>
								<label for="name{$names.i}">{$lblName|ucfirst} {$names.i}</label>
								{$names.txtName} {$names.txtNameError}
							</p>
							{/iteration:names}
						</div>
						<div class="options">
							<p>
								<label for="format">{$lblLayout|ucfirst}</label>
								{$txtFormat} {$txtFormatError}
							</p>

							<ul class="inputList">
								<li>{$chkActive} <label for="active">{$lblActive|ucfirst}</label> {$chkActiveError}</li>
								<li>{$chkDefault} <label for="default">{$msgIsDefault}</label> {$chkDefaultError}</li>
							</ul>
						</div>
					</div>

					<div class="fullwidthOptions">
						<div class="buttonHolderRight">
							<input id="add" class="inputButton button mainButton" type="submit" name="add" value="{$lblAddTemplate|ucfirst}" />
						</div>
					</div>
				{/form:add}
			</div>
		</td>
	</tr>
</table>
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}