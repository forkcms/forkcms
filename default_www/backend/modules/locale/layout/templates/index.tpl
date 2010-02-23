{include:file='{$BACKEND_CORE_PATH}/layout/templates/header.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl'}
		<td id="contentHolder">
			<div id="statusBar">
				<p class="breadcrumb">{$lblLocale|ucfirst} &gt; {$lblOverview|ucfirst}</p>
			</div>

			<div class="inner">
				<!-- @todo tijs check me -->

					<script type="text/javascript" charset="utf-8">
						$(document).ready(function() {

							// Add last child and first child for IE
							$('.datafilter tbody td:first-child').addClass('firstChild')
							$('.datafilter tbody td:last-child').addClass('lastChild')

							// Make sure all options are the same height (eq height cols)
							function equalHeight(group) {
								var tallest = 0;
								group.each(function() {
									var thisHeight = $(this).height();
									if(thisHeight > tallest) {
										tallest = thisHeight;
									}
								});
								group.height(tallest);
							}

							equalHeight($(".datafilter tbody .options"));
						});

					</script>


					<div class="datagridHolder">
						<div class="tableHeading">
							<h3>{$lblTranslations|ucfirst}</h3>
							<div class="buttonHolderRight">
								<a href="{$var|geturl:'add'}&language={$language}&application={$application}&module={$module}&type={$type}&name={$name}&value={$value}" class="button icon iconAdd"><span><span><span>{$lblAddTranslation|ucfirst}</span></span></span></a>
							</div>
						</div>

						{form:filter}
						<div class="datafilter">
							<table>
								<tbody>
									<tr>
										<td>
											<div class="options">
												<p>
													<label for="language">{$lblLanguage|ucfirst}</label>
													{$ddmLanguage} {$ddmLanguageError}
												</p>
												<p>
													<label for="application">{$lblApplication|ucfirst}</label>
													{$ddmApplication} {$ddmApplicationError}
												</p>
											</div>
										</td>
										<td>
											<div class="options">
												<p>
													<label for="module">{$lblModule|ucfirst}</label>
													{$ddmModule} {$ddmModuleError}
												</p>
												<p>
													<label for="type">{$lblType|ucfirst}</label>
													{$ddmType} {$ddmTypeError}
												</p>
											</div>
										</td>
										<td>
											<div class="options">
												<div class="oneLiner">
													<p>
														<label for="name">
															{$lblName|ucfirst}
														</label>
													</p>
													<p>
														<abbr class="help">(?)</abbr>
														<span class="balloon balloonAlt" style="display: none;">
															{$msgNameHelpTxt}
														</span>
													</p>
												</div>
												{$txtName} {$txtNameError}

												<div class="oneLiner">
													<p>
														<label for="value">{$lblValue|ucfirst}</label>
													</p>
													<p>
														<abbr class="help">(?)</abbr>
														<span class="balloon balloonAlt" style="display: none;">
															{$msgValueHelpTxt}
														</span>
													</p>
												</div>
												{$txtValue} {$txtValueError}

											</div>
										</td>
									</tr>
								</tbody>
								<tfoot>
									<tr>
										<td colspan="99">
											<div class="options">
												<div class="buttonHolder">
													<input id="search" class="inputButton button mainButton" type="submit" name="search" value="{$lblUpdateFilter|ucfirst}" />
												</div>
											</div>
										</td>
									</tr>
								</tfoot>
							</table>
						</div>
						{/form:filter}

						{option:datagrid}
						<form action="{$var|geturl:'mass_action'}" method="get" class="forkForms submitWithLink" id="massLocaleAction">
							<input type="hidden" name="offset" value="{$offset}" />
							<input type="hidden" name="order" value="{$order}" />
							<input type="hidden" name="sort" value="{$sort}" />
							<input type="hidden" name="language" value="{$language}" />
							<input type="hidden" name="application" value="{$application}" />
							<input type="hidden" name="module" value="{$module}" />
							<input type="hidden" name="type" value="{$type}" />
							<input type="hidden" name="name" value="{$name}" />
							<input type="hidden" name="value" value="{$value}" />
							{$datagrid}
						</form>
						{/option:datagrid}
					</div>


					{option:!datagrid}
					<!-- @todo check me plz. -->
					<div class="datagridHolder">
						<div class="tableHeading">
							<h3>{$lblTranslations|ucfirst}</h3>
							<div class="buttonHolderRight">
								<a href="{$var|geturl:'add'}&language={$language}&application={$application}&module={$module}&type={$type}&name={$name}&value={$value}" class="button icon iconAdd"><span><span><span>{$lblAdd|ucfirst}</span></span></span></a>
							</div>
						</div>
						<table border="0" cellspacing="0" cellpadding="0" class="datagrid">
							<tr>
								<td>{$msgNoItems}</td>
							</tr>
						</table>
					</div>
					{/option:!datagrid}
				</div>
			</div>

		</td>
	</tr>
</table>
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}