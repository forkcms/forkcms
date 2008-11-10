				<div id="searchform">
					<form action="{$searchformLink}" method="post" id="search">
					<input type="hidden" name="form" value="search" />
						<fieldset>
							<div class="loop"></div>
							<input type="text" name="searchterm" value="{$lblFillInSearchterm|ucfirst}&hellip;" onclick="if (value=='{$lblFillInSearchterm|ucfirst}&hellip;') value=''" id="searchterm" class="input-text" />
							<input type="submit" name="submit" value="{$lblSearch|ucfirst}" class="input-submit" value="{$lblSearch}" />
						</fieldset>
					</form>
				</div>