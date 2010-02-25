{include:file='{$BACKEND_CORE_PATH}/layout/templates/header.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl'}
		<td id="contentHolder">
			<div id="statusBar">
				<p class="breadcrumb">{$lblPages|ucfirst} &gt; {$lblEdit|ucfirst}</p>
			</div>

			<div class="inner">
				{form:edit}
					<div class="box horizontal">
						<div class="heading">
							{* @todo add label *}
							<h3>Bewerk template</h3>
						</div>

						<div class="options">
							<p>
								<label for="path">{$lblPath|ucfirst}</label>
								{$txtPath} {$txtPathError}
								{* @todo add label *}
								<span class="helpTxt">vb:. core/layout/templates/home.tpl</span>
							</p>
							<p>
								<label for="label">{$lblLabel|ucfirst}</label>
								{$txtLabel} {$txtLabelError}
							</p>
							<p>
								<label for="format">{$lblLayout|ucfirst}</label>
								{$txtFormat} {$txtFormatError}
								{* @todo add label *}
								<span class="helpTxt">e.g. [0,1],[2,none]</span>
							</p>
						</div>
						<div class="options">
							<p>
								<label for="num_blocks">{$lblNumberOfBlocks|ucfirst}</label>
								{$ddmNumBlocks} {$ddmNumBlocksError}
							</p>
							{iteration:names}
							<p class="blockName">
								<label for="name{$names.i}">{$lblName|ucfirst} {$names.i}</label>
								{$names.txtName} {$names.txtNameError}
							</p>
							{/iteration:names}
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
							<input id="edit" class="inputButton button mainButton" type="submit" name="edit" value="{$lblEditTemplate|ucfirst}" />
						</div>
					</div>
				{/form:edit}
			</div>
		</td>
	</tr>
</table>
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}