			<div id="user">
				{option:oLoggedOn}<p>{$lblLoggedOnAs|ucfirst} {$onsiteUserName}. <a href="{$logOutUrl}">{$lblLogOut|ucfirst}</a></p>{/option:oLoggedOn}
				{option:oLoggedOff}
				<form action="{$logOnUrl}" method="post">
					<fieldset>
						<p>
							<input type="hidden" name="form" value="login" />
							<input type="hidden" name="redirect" value="{$redirect}" />
							<label for="username">{$lblUsername|ucfirst}<abbr title="{$lblRequired|ucfirst}">*</abbr></label>
							<input type="text" id="onsite_username" name="onsite_username" value="" maxlength="255" class="input-text" />
							<label for="password">{$lblPassword|ucfirst}<abbr title="{$lblRequired|ucfirst}">*</abbr></label>
							<input type="password" id="onsite_password" name="onsite_password" value="" maxlength="255" class="input-password" />
							<input type="submit" class="input-submit" name="submit" value="{$lblLogin|ucfirst}" />
						</p>
					</fieldset>
				</form>
				{/option:oLoggedOff}
			</div>